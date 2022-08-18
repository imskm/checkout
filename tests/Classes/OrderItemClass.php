<?php

namespace Fantom\Checkout\Tests\Classes;

use Fantom\Database\Model;
use Fantom\Checkout\Traits\OrderItemTrait;
use Fantom\Checkout\Tests\Classes\ProductClass;
use Fantom\Checkout\Interfaces\ProductInterface;
use Fantom\Checkout\Interfaces\OrderItemInterface;

/**
 * OrderItemClass
 */
class OrderItemClass extends Model implements OrderItemInterface
{
    protected $table = "order_items";
    protected $primary = "id";

	use OrderItemTrait;

    public function getProductFor(int $product_id): ?ProductInterface
    {
        $product = ProductClass::find($product_id);

        if (is_null($product)) {
            return null;
        }

        if ($product->first()) {
            $product = $product->first();
        }

        return $product;
    }







    public function thisId()
    {
        if ($this->lastId()) {
            return $this->lastId();
        }

        return $this->id;
    }
}