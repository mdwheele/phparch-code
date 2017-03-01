<?php

namespace Sample\Support;

use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    /** @test */
    public function it_can_create_a_new_uuid_from_string()
    {
        $uuid = Uuid::fromString('ff6f8cb0-c57d-11e1-9b21-0800200c9a66');

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertEquals('ff6f8cb0-c57d-11e1-9b21-0800200c9a66', $uuid->toString());
    }
}
