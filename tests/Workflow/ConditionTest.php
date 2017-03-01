<?php

namespace Sample\Workflow;

use Sample\Workflow\Condition;
use Sample\Workflow\Task;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    use NodeContractTests;

    private function getNodeImplementation()
    {
        return new Condition();
    }

    private function getOtherNodeImplementation()
    {
        return new Task();
    }
}
