<?php

namespace Sample\Workflow;

use Sample\Workflow\Condition;
use Sample\Workflow\Marking;
use PHPUnit\Framework\TestCase;

class MarkingTest extends TestCase
{
    /** @test */
    public function it_has_no_initial_marking()
    {
        $marking = new Marking();

        $this->assertTrue($marking->isUnmarked());
    }

    /** @test */
    public function many_conditions_can_be_marked()
    {
        $marking = new Marking();

        $marking->mark($a = new Condition(), $b = new Condition());

        $this->assertTrue($marking->has($a, $b));
    }

    /** @test */
    public function conditions_can_be_removed_from_marking()
    {
        $marking = new Marking();

        $marking->mark($a = new Condition(), $b = new Condition());
        $marking->unmark($b);

        $this->assertTrue($marking->has($a));
        $this->assertFalse($marking->has($b));
    }

    /** @test */
    public function number_of_conditions_marked_can_be_queried()
    {
        $marking = new Marking();
        $marking->mark(new Condition(), new Condition());

        $this->assertEquals(2, $marking->count());
    }
}
