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
     * @var AttributeStore
     */
    public $attributes;

    /**
     * @var Marking
     */
    public $marking;

    public function __construct(Uuid $caseNumber, ProcessDefinition $definition, Marking $marking, AttributeStore $attributes)
    {
        $this->caseNumber = $caseNumber;
        $this->definition = $definition;
        $this->attributes = $attributes;
        $this->marking = $marking;
    }
}
