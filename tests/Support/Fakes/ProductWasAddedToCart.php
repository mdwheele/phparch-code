<?php

namespace Sample\Support\Fakes;

use Sample\Support\Uuid;

class ProductWasAddedToCart
{
    /**
     * @var Uuid
     */
    public $id;

    /**
     * @var string
     */
    public $sku;

    public function __construct(Uuid $id, $sku)
    {
        $this->id = $id;
        $this->sku = $sku;
    }
}