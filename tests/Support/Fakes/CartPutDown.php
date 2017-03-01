<?php

namespace Sample\Support\Fakes;

use Workflow\Support\Uuid;

class CartPutDown
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