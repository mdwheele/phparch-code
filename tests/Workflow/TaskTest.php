<?php

namespace Sample\Workflow;

use Sample\Support\AttributeStore;
use Sample\Workflow\Condition;
use Sample\Workflow\Task;
use Sample\Workflow\Triggers\Automatic;
use Sample\Workflow\Triggers\UserInput;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    use NodeContractTests;

    private function getNodeImplementation()
    {
        return new Task();
    }

    private function getOtherNodeImplementation()
    {
        return new Condition();
    }

    /** @test */
    public function it_can_have_an_automatic_trigger()
    {
        $task = Task::automatic();

        $this->assertInstanceOf(Automatic::class, $task->getTrigger());
        $this->assertTrue($task->getTrigger()->isSatisfied());
    }

    /** @test */
    public function it_can_have_a_user_trigger()
    {
        $task = Task::user();
        $trigger = $task->getTrigger();

        $caseAttributes = new AttributeStore([
            'inputs' => [
                $task->getId()
            ]
        ]);

        $this->assertInstanceOf(UserInput::class, $trigger);
        $this->assertTrue($trigger->isSatisfied($caseAttributes));
    }
}
