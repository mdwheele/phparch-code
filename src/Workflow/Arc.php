<?php

namespace Sample\Workflow;

class Arc
{
    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var Task
     */
    private $task;

    public function __construct(Condition $condition, Task $task)
    {
        $this->condition = $condition;
        $this->task = $task;
    }

    public function getCondition()
    {
        return $this->condition;
    }

    public function getTask()
    {
        return $this->task;
    }
}
