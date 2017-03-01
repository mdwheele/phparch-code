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
                "The case-number was '{$event->caseNumber}'.",
                $this->screenshot(),
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
                "Input was provided to '{$event->taskId}''.",
                $this->screenshot(),
                $event->attributes
            )
        );
    }

    protected function applyWorkflowTaskEnabled(WorkflowTaskEnabled $event)
    {
        $this->simulation->add(
            new Step(
                "Workflow Task Enabled",
                $event->taskId->toString(),
                $this->screenshot(),
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
                $event->taskId->toString(),
                $this->screenshot(),
                $this->attributes
            )
        );
    }

    protected function applyWorkflowCompleted(WorkflowCompleted $event)
    {
        $this->simulation->add(
            new Step(
                "Workflow Finished",
                "A workflow is finished when its 'sink' is marked.",
                $this->screenshot(),
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
}