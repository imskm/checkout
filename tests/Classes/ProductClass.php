<?php

namespace Fantom\Checkout\Tests\Classes;

use Fantom\Database\Model;
use Fantom\Checkout\Traits\ProductTrait;
use Fantom\Checkout\Interfaces\ProductInterface;

/**
 * ProductClass
 */
class ProductClass extends Model implements ProductInterface
{
    protected $table = "products";
    protected $primary = "id";

    use ProductTrait;

    public static function make(array $data)
    {
        $product = new self;

        $product->title    = $data['title'];
        $product->slug     = str_replace(" ", "-", strtolower($data['title']));
        $product->price_mp = $data['price_mp'];
        $product->price_sp = $data['price_sp'];
        $product->category_id = 1;

        return $product;
    }

	public function markedPrice()
    {
        return $this->price_mp / 100;
    }

    public function salePrice()
    {
        return $this->price_sp / 100;
    }

    public function discount()
    {
        return $this->markedPrice() - $this->salePrice();
    }

    public function stock()
    {
        return $this->stock;
    }

    public function tax()
    {
        return 0;

        // @NOTE Inclusive in product pirce

        return $this->salePrice() / 118 * 0.18;
    }








    public function thisId()
    {
        if ($this->lastId()) {
            return $this->lastId();
        }

        return $this->id;
    }
}