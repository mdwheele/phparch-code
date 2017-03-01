<?php

namespace Sample\Workflow\Triggers;

use Sample\Workflow\Triggers\Automatic;
use PHPUnit\Framework\TestCase;

class AutomaticTest extends TestCase
{

    /** @test */
    public function it_is_always_satisfied()
    {
        $trigger = new Automatic();

        $this->assertTrue($trigger->isSatisfied());
    }
}
