<?php

namespace Sample\Workflow;

use Sample\Support\AggregateRoot;
use Sample\Support\AttributeStore;
use Sample\Support\Uuid;
use Sample\Workflow\Events\InputWasProvided;
use Sample\Workflow\Events\WorkflowCompleted;
use Sample\Workflow\Events\WorkflowStarted;
use Sample\Workflow\Events\WorkflowTaskEnabled;
use Sample\Workflow\Events\WorkflowTaskFired;
use Sample\Workflow\Exception\WorkflowExecutionException;

class Workflow extends AggregateRoot
{
    /**
     * @var Uuid represents identity for an individual execution of process definition
     */
    private $caseNumber;

    /**
     * @var ProcessDefinition the process definition that the Workflow is executing
     */
    private $definition;

    /**
     * @var Marking represents token placement in a process definition
     */
    private $marking;

    /**
     * @var AttributeStore
     */
    private $attributes;

    /**
     * Begins a new workflow execution, given a process definition to run.
     *
     * @param ProcessDefinition $definition
     *
     * @return Workflow
     */
    public static function start(ProcessDefinition $definition)
    {
        $instance = new Workflow();

        $marking = new Marking();
        $marking->mark($definition->getSource());

        $instance->apply(new WorkflowStarted(Uuid::make(), $definition, $marking, new AttributeStore()));

        foreach ($instance->getTasksEnabledBy($definition->getSource()) as $task) {
            $instance->apply(new WorkflowTaskEnabled($instance->caseNumber, $task->getId(), $task->getTriggerType()));
        }

        $instance->tick();

        return $instance;
    }

    /**
     * Provide user input for a specific task.
     *
     * @param Uuid  $taskId
     * @param array $attributes
     */
    public function input(Uuid $taskId, array $attributes = [])
    {
        $inputs = $this->attributes->get('inputs');
        $inputs[] = $taskId->toString();
        $this->attributes->set('inputs', $inputs);

        $this->apply(new InputWasProvided($this->caseNumber, $taskId, $this->attributes));

        $this->tick();
    }

    /**
     * Tick is responsible for moving workflow state as far as possible
     * in a single transaction.
     *
     * @internal
     * @throws WorkflowExecutionException
     */
    public function tick()
    {
        foreach ($this->getEnabledTasks() as $task) {
            // We check again in case firing of a task consumes token
            // that previously enabled a task.
            if (! $this->isTaskEnabled($task)) {
                return;
            }

            $this->fireTask($task);

            if ($this->marking->has($this->definition->getSink())) {
                if ($this->marking->count() > 1) {
                    throw new WorkflowExecutionException('Workflow completed while other tasks were enabled.');
                }

                $this->apply(new WorkflowCompleted($this->caseNumber));
            }
        }
    }

    /**
     * @return Task[]
     */
    private function getEnabledTasks()
    {
        $tasks = [];

        foreach ($this->definition->getTasks() as $task) {
            if ($this->isTaskEnabled($task)) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    /**
     * @param Task $task
     *
     * @return mixed
     */
    private function isTaskEnabled(Task $task)
    {
        if (count($task->getInputArcs()) == 0) {
            return false;
        }

        foreach ($task->getInputArcs() as $arc) {
            if ($this->marking->has($arc->getCondition()) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * When enabled, a task may fire.
     *
     * This consumes a Token from every input Condition and
     * produces a Token in every output Condition.
     *
     * @param Task $task
     * @throws WorkflowExecutionException
     */
    private function fireTask(Task $task)
    {
        $trigger = $task->getTrigger();
        $tasksEnabledByFiring = [];

        if (! $trigger->isSatisfied($this->attributes)) {
            return;
        }

        foreach ($task->getInputArcs() as $arc) {
            $this->marking->unmark($arc->getCondition());
        }

        foreach ($task->getOutputArcs() as $arc) {
            $this->marking->mark($arc->getCondition());
            $tasksEnabledByFiring = array_merge($tasksEnabledByFiring, $this->getTasksEnabledBy($arc->getCondition()));
        }

        $this->apply(new WorkflowTaskFired($this->caseNumber, $task->getId(), Marking::fromMarking($this->marking)));

        foreach ($tasksEnabledByFiring as $task) {
            $this->apply(new WorkflowTaskEnabled($this->caseNumber, $task->getId(), $task->getTriggerType()));
            $this->fireTask($task);
        }
    }

    /**
     * @param Condition $condition
     *
     * @return Task[]
     */
    private function getTasksEnabledBy(Condition $condition)
    {
        $tasks = [];

        foreach ($condition->getOutputArcs() as $arc) {
            if ($this->isTaskEnabled($arc->getTask())) {
                $tasks[] = $arc->getTask();
            }
        }

        return $tasks;
    }

    protected function applyWorkflowStarted(WorkflowStarted $event)
    {
        $this->caseNumber = deep_copy($event->caseNumber);
        $this->definition = deep_copy($event->definition);
        $this->marking = deep_copy($event->marking);
        $this->attributes = deep_copy($event->attributes);
    }

    protected function applyInputWasProvided(InputWasProvided $event)
    {
        $this->attributes = deep_copy($event->attributes);
    }

    protected function applyWorkflowTaskFired(WorkflowTaskFired $event)
    {
        $this->marking = deep_copy($event->marking);
    }

    public function getId()
    {
        return $this->caseNumber;
    }
}
