<?php

namespace Sample\Workflow;

use Sample\Workflow\Condition;
use Sample\Workflow\ProcessDefinition;
use Sample\Workflow\Task;
use PHPUnit\Framework\TestCase;

class ProcessDefinitionTest extends TestCase
{
    /** @test */
    public function the_first_condition_added_is_the_source()
    {
        $definition = new ProcessDefinition();

        $definition->addCondition($c1 = new Condition)
            ->addTask($c1, $t = new Task, $c2 = new Condition)
            ->setSink($c2);

        $this->assertSame($c1, $definition->getSource());

        $this->assertCount(1, $definition->getTasks());
        $this->assertSame($t, $definition->getTasks()[0]);
    }
}
