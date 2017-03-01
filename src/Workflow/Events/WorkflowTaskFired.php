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
     * @var Marking
     */
    public $marking;

    /**
     * @var Uuid
     */
    public $taskId;

    public function __construct(Uuid $caseNumber, Uuid $taskId, Marking $marking)
    {
        $this->caseNumber = $caseNumber;
        $this->marking = $marking;
        $this->taskId = $taskId;
    }
}
