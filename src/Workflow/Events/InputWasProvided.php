<?php

namespace Sample\Workflow\Events;

use Sample\Support\AttributeStore;
use Sample\Support\Uuid;

class InputWasProvided
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
     * @var AttributeStore
     */
    public $attributes;

    public function __construct(Uuid $caseNumber, Uuid $taskId, AttributeStore $attributes)
    {
        $this->caseNumber = deep_copy($caseNumber);
        $this->taskId = deep_copy($taskId);
        $this->attributes = deep_copy($attributes);
    }
}