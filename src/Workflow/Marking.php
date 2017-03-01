<?php

namespace Sample\Workflow;

use Illuminate\Support\Collection;

/**
 * Responsible for maintaining the complete marking for
 * for some process definition as part of workflow execution
 */
class Marking
{
    /**
     * @var Collection
     */
    private $conditions;

    public function __construct(array $marking = [])
    {
        $this->conditions = new Collection($marking);
    }

    public static function fromMarking(Marking $marking)
    {
        return new self($marking->conditions->all());
    }

    /**
     * Mark tokens that exist in one or many conditions
     *
     * @param Condition[] ...$conditions
     */
    public function mark(Condition... $conditions)
    {
        foreach ($conditions as $condition) {
            $this->conditions[$condition->getId()->toString()] = 1;
        }
    }

    /**
     * Remove tokens that exist in one or many conditions
     *
     * @param Condition[] ...$conditions
     */
    public function unmark(Condition... $conditions)
    {
        foreach ($conditions as $condition) {
            unset($this->conditions[$condition->getId()->toString()]);
        }
    }

    /**
     * Check to see if the marking has tokens in one or many conditions
     *
     * @param Condition[] ...$conditions
     *
     * @return bool
     */
    public function has(Condition... $conditions)
    {
        foreach ($conditions as $condition) {
            $conditionId = $condition->getId()->toString();

            if (! $this->conditions->has($conditionId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return int the number of conditions marked
     */
    public function count()
    {
        return $this->conditions->count();
    }

    public function isUnmarked()
    {
        return $this->conditions->isEmpty();
    }
}
