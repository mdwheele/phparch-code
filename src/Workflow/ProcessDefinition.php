<?php

namespace Sample\Workflow;

use Illuminate\Support\Collection;

/**
 * Represents a common interface over a node graph of Tasks, Conditions
 * and Arcs between them. It also maintains the source and sink requirements
 * of a WF-net.
 */
class ProcessDefinition
{
    /**
     * @var Collection
     */
    private $conditions;

    /**
     * @var Collection
     */
    private $tasks;

    /**
     * @var Condition
     */
    private $source;

    /**
     * @var Condition
     */
    private $sink;

    public function __construct()
    {
        $this->conditions = new Collection();
        $this->tasks = new Collection();
    }

    public function addCondition(Condition $condition)
    {
        if (is_null($this->source)) {
            $this->source = $condition;
        }

        $this->conditions[$condition->getId()->toString()] = $condition;

        return $this;
    }

    public function addTask(Condition $from, Task $task, Condition $to)
    {
        $this->addCondition($from);
        $this->addCondition($to);

        $from->flowInto($task);
        $to->flowOutOf($task);

        $this->tasks[$task->getId()->toString()] = $task;

        return $this;
    }

    public function addOutputCondition(Task $task, Condition $condition)
    {
        $this->addCondition($condition);

        $this->tasks[$task->getId()->toString()]->flowInto($condition);

        return $this;
    }

    public function setSource(Condition $source)
    {
        $this->source = $source;

        return $this;
    }

    public function setSink(Condition $sink)
    {
        $this->sink = $sink;

        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getSink()
    {
        return $this->sink;
    }

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        return $this->tasks->values()->all();
    }

    /**
     * @return Condition[]
     */
    public function getConditions()
    {
        return $this->conditions->values()->all();
    }

}
