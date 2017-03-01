<?php

namespace Sample\Workflow\Events;

use Sample\Support\Uuid;

class WorkflowTaskEnabled
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
     * @var string
     */
    public $triggerType;

    public function __construct(Uuid $caseNumber, Uuid $taskId, $triggerType)
    {
        $this->caseNumber = deep_copy($caseNumber);
        $this->taskId = deep_copy($taskId);
        $this->triggerType = $triggerType;
    }
}
