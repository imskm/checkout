<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fantom\Checkout\Tests\Classes\ProductClass;
use Fantom\Checkout\Tests\Classes\OrderItemClass;
use Fantom\Checkout\Interfaces\OrderItemInterface;


/**
 * 
 */
class OrderItemTest extends TestCase
{
	public function test_order_item_object_can_be_created()
	{
		$this->assertInstanceOf(OrderItemClass::class, new OrderItemClass());

		$this->assertInstanceOf(OrderItemInterface::class, new OrderItemClass());
	}

	public function test_order_item_can_be_created_with_product_correctly()
	{
		$product = ProductClass::make([
			'id'		=> 111,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 90000,
			'stock'		=> 100,
		]);

		$order_item = new OrderItemClass();

		$order_item->addProduct($product, $qty = 5);

		$this->assertSame($product, $order_item->product());
		$this->assertSame($qty, $order_item->qty());
		$this->assertSame(1000, $order_item->markedPrice());
		$this->assertSame(900, $order_item->salePrice());
		$this->assertSame(1000 * $qty, $order_item->totalMarkedPrice());
		$this->assertSame(900 * $qty, $order_item->totalSalePrice());
		$this->assertSame($discount = 100, $order_item->discount());
	}
}