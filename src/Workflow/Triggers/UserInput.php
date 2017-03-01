<?php

namespace Sample\Workflow\Triggers;

use Sample\Support\AttributeStore;
use Sample\Support\Uuid;

class UserInput implements Trigger
{
    /**
     * @var Uuid
     */
    private $taskId;

    public function __construct(Uuid $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @param AttributeStore $attributes
     * @return bool
     */
    public function isSatisfied($attributes = null)
    {
        $inputs = $attributes->get("inputs");

        if (!$inputs) {
            return false;
        }

        return in_array($this->taskId->toString(), $inputs);
    }
}