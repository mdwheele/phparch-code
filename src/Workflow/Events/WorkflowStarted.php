<?php

namespace Sample\Workflow\Events;

use Sample\Support\AttributeStore;
use Sample\Support\Uuid;
use Sample\Workflow\Marking;
use Sample\Workflow\ProcessDefinition;

class WorkflowStarted
{
    /**
     * @var Uuid
     */
    public $caseNumber;

    /**
     * @var ProcessDefinition
     */
    public $definition;

    /**
     * @var Marking
     */
    public $marking;

    /**
     * @var AttributeStore
     */
    public $attributes;

    public function __construct(Uuid $caseNumber, ProcessDefinition $definition, Marking $marking, AttributeStore $attributes)
    {
        $this->caseNumber = deep_copy($caseNumber);
        $this->definition = deep_copy($definition);
        $this->marking = deep_copy($marking);
        $this->attributes = deep_copy($attributes);
    }
}
