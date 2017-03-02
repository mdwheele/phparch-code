<?php

namespace Sample\Workflow\ReadModel\Simulation;

use Sample\Support\AttributeStore;
use Sample\Support\Projector;
use Sample\Workflow\Events\InputWasProvided;
use Sample\Workflow\Events\WorkflowCompleted;
use Sample\Workflow\Events\WorkflowStarted;
use Sample\Workflow\Events\WorkflowTaskEnabled;
use Sample\Workflow\Events\WorkflowTaskFired;
use Sample\Workflow\Marking;
use Sample\Workflow\ProcessDefinition;
use Sample\Workflow\ReadModel\GraphvisDumper;

class SimulationProjector extends Projector
{
    /**
     * @var Simulation
     */
    private $simulation;

    /**
     * @var AttributeStore
     */
    private $attributes;

    /**
     * @var Marking
     */
    private $marking;

    /**
     * @var ProcessDefinition
     */
    private $definition;

    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
    }

    protected function applyWorkflowStarted(WorkflowStarted $event)
    {
        $this->definition = $event->definition;
        $this->marking = $event->marking;
        $this->attributes = $event->attributes;

        $this->simulation->add(
            new Step(
                "Workflow Started",
                "The workflow case number is <kbd>{$event->caseNumber}</kbd>.",
                $this->screenshot(),
                $this->graph(),
                $event->attributes
            )
        );
    }

    protected function applyInputWasProvided(InputWasProvided $event)
    {
        $this->attributes = $event->attributes;

        $this->simulation->add(
            new Step(
                "Input Was Provided",
                "User input was provided to '{$event->taskId}''.",
                $this->screenshot(),
                $this->graph(),
                $event->attributes
            )
        );
    }

    protected function applyWorkflowTaskEnabled(WorkflowTaskEnabled $event)
    {
        $this->simulation->add(
            new Step(
                "Workflow Task Enabled",
                "<kbd>{$event->taskId->toString()}</kbd> became enabled.",
                $this->screenshot(),
                $this->graph(),
                $this->attributes
            )
        );
    }

    protected function applyWorkflowTaskFired(WorkflowTaskFired $event)
    {
        $this->marking = $event->marking;

        $this->simulation->add(
            new Step(
                "Workflow Task Fired",
                "<kbd>{$event->taskId->toString()}</kbd> was fired.",
                $this->screenshot(),
                $this->graph(),
                $this->attributes
            )
        );
    }

    protected function applyWorkflowCompleted(WorkflowCompleted $event)
    {
        $this->simulation->add(
            new Step(
                "Workflow Finished",
                "",
                $this->screenshot(),
                $this->graph(),
                $this->attributes
            )
        );
    }

    private function screenshot()
    {
        $dumper = new GraphvisDumper();

        return $dumper->createImageSrc(
            $dumper->dump($this->definition, $this->marking)
        );
    }

    private function graph()
    {
        $dumper = new GraphvisDumper();

        return $dumper->dumpArray($this->definition, $this->marking);
    }
}