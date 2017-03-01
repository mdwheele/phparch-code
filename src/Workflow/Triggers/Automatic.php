<?php

namespace Sample\Workflow\Triggers;

class Automatic implements Trigger
{
    public function isSatisfied($context = null)
    {
        return true;
    }
}