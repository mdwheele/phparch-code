<?php

namespace Sample\Support\Fakes;

use Sample\Support\Uuid;

class CartPickedUp
{
    /**
     * @var Uuid
     */
    public $id;

    public function __construct(Uuid $id)
    {
        $this->id = $id;
    }
}