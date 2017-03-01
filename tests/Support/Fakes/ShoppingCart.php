<?php

namespace Sample\Support\Fakes;

use Sample\Support\AggregateRoot;
use Sample\Support\Uuid;

class ShoppingCart extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string[]
     */
    private $productSkus = [];

    public static function pickup()
    {
        $cart = new static();

        $cart->apply(new CartPickedUp(Uuid::make()));

        return $cart;
    }

    public function addToCart($productSku)
    {
        if (in_array($productSku, $this->productSkus)) {
            throw new \Exception('I am sooo angry. You already have this product!');
        }

        $this->apply(new ProductWasAddedToCart($this->uuid, $productSku));
    }

    public function putDown()
    {
        if (count($this->productSkus) === 0) {
            throw new \Exception('You should shop more. Thanks.');
        }

        $this->apply(new CartPutDown($this->uuid));
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->uuid->toString();
    }

    protected function applyCartPickedUp(CartPickedUp $event)
    {
        $this->uuid = $event->id;
    }

    protected function applyProductWasAddedToCart(ProductWasAddedToCart $event)
    {
        $this->productSkus[] = $event->sku;
    }
}