<?php

namespace Sample\Workflow\Events;

use Sample\Support\Uuid;

class WorkflowCompleted
{
    /**
     * @var Uuid
     */
    public $caseNumber;

    public function __construct(Uuid $caseNumber)
    {
        $this->caseNumber = deep_copy($caseNumber);
    }
}
