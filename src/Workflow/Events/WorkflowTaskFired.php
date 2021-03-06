<?php

namespace Sample\Workflow\Events;

use Sample\Support\Uuid;
use Sample\Workflow\Marking;

class WorkflowTaskFired
{
    /**
     * @var Uuid
     */
    public $caseNumber;

    /**
     * @var Uuid
     */
    public $taskId;

    /**
     * @var Marking
     */
    public $marking;

    public function __construct(Uuid $caseNumber, Uuid $taskId, Marking $marking)
    {
        $this->caseNumber = deep_copy($caseNumber);
        $this->taskId = deep_copy($taskId);
        $this->marking = deep_copy($marking);
    }
}
