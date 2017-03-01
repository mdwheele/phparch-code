<?php

namespace Sample\Support;

use Ramsey\Uuid\Uuid as UuidImpl;

/**
 * Uuid wraps Ramsey\Uuid\Uuid to force usage of version 4 (random)
 * UUIDs. It should be used by any applicable value object that
 * represents identity rather than using the third-party implementation
 * directly.
 */
class Uuid
{
    /**
     * @var UuidImpl
     */
    private $uuid;

    public static function make()
    {
        $instance = new static();
        $instance->uuid = UuidImpl::uuid4();
        return $instance;
    }

    public static function fromString($stringUuid)
    {
        $instance = new static();
        $instance->uuid = UuidImpl::fromString($stringUuid);
        return $instance;
    }

    public function __toString()
    {
        return $this->uuid->toString();
    }

    public function toString()
    {
        return $this->__toString();
    }
}