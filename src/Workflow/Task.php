<?php

namespace Sample\Workflow;

use Sample\Workflow\Triggers\Automatic;
use Sample\Workflow\Triggers\Trigger;
use Sample\Workflow\Triggers\UserInput;

class Task extends Node
{
    /**
     * @var Trigger
     */
    protected $trigger;

    public function __construct(Trigger $trigger = null)
    {
        parent::__construct();

        $this->trigger = $trigger;

        if (is_null($trigger)) {
            $this->trigger = new Automatic();
        }
    }

    /**
     * Create a new automatically-triggered task.
     *
     * @return Task
     */
    public static function automatic()
    {
        return new Task(new Automatic());
    }

    /**
     * Create a new user-input triggered task.
     *
     * @return Task
     */
    public static function user()
    {
        $instance = new Task();
        $instance->trigger = new UserInput($instance->getId());

        return $instance;
    }

    /**
     * @return Trigger
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    public function getTriggerType()
    {
        $trigger = $this->getTrigger();

        if ($trigger instanceof Automatic) {
            return Trigger::AUTOMATIC;
        }

        if ($trigger instanceof UserInput) {
            return Trigger::USER_INPUT;
        }
    }
}
