<?php

namespace Sample\Support;

use Exception;
use PHPUnit\Framework\TestCase;
use Sample\Support\Fakes\ShoppingCart;

class AggregateRootTest extends TestCase
{

    /** @test */
    public function shopping_cart_can_be_picked_up_and_have_products_added()
    {
        // Cart was picked up
        $cart = ShoppingCart::pickup();

        // Product was added to cart
        $cart->addToCart('apple');

        // After being picked up and adding a product
        // we should have two events
        $events = $cart->getUncommittedEvents();

        $this->assertNotEmpty($cart->getId());
        $this->assertCount(2, $events);
    }

    /** @test */
    public function cannot_put_down_a_cart_without_buying_something()
    {
        $cart = ShoppingCart::pickup();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You should shop more.');

        $cart->putDown();
    }

    /** @test */
    public function adding_more_than_one_type_of_product_makes_the_cart_angry()
    {
        $cart = ShoppingCart::pickup();
        $cart->addToCart('apple');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('I am sooo angry.');

        $cart->addToCart('apple');
    }
}