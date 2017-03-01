<?php

namespace Sample\Workflow\Triggers;

class Never implements Trigger
{
    public function isSatisfied($context = null)
    {
        return false;
    }
}