<?php

namespace Sample\Workflow\Triggers;

interface Trigger
{
    const AUTOMATIC = 'automatic';
    const USER_INPUT = 'user_input';
    const TIME = 'time';

    /**
     * Determine whether or not the trigger is satisfied.
     *
     * @param mixed $context
     *
     * @return bool
     */
    public function isSatisfied($context = null);
}