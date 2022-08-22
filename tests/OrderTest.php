<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fantom\Checkout\Tests\Classes\UserClass;
use Fantom\Checkout\Tests\Classes\OrderClass;
use Fantom\Checkout\Tests\Classes\ProductClass;
use Fantom\Checkout\Tests\Classes\OrderItemClass;

/**
 * 
 */
class OrderTest extends TestCase
{
	public function test_order_object_can_be_created()
	{
		$this->assertInstanceOf(OrderClass::class, new OrderClass());
	}

	public function test_complete_order_object_creation()
	{
		$product_1 = ProductClass::make([
			'id'		=> 1,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 90000,
			'stock'		=> 100,
		]);
		$product_2 = ProductClass::make([
			'id'		=> 2,
			'title' 	=> 'Roadster Men T-Shirt 001',
			'price_mp' 	=> 60000,
			'price_sp' 	=> 45000,
			'stock'		=> 100,
		]);

		$order_item_1 = new OrderItemClass();
		$order_item_2 = new OrderItemClass();

		$order_item_1->addProduct($product_1);
		$order_item_2->addProduct($product_2, $qty = 2);

		$order = new OrderClass();

		$order->addOrderItem($order_item_1);
		$order->addOrderItem($order_item_2);

		$this->assertIsArray($order->orderItems());

		$this->assertNotEmpty($order->orderItems());
		$this->assertContainsOnlyInstancesOf(OrderItemClass::class, $order->orderItems());

		// Check order values
		$this->assertSame(2200, $order->grossTotal());
		$this->assertSame(1800, $order->subTotal());
		$this->assertSame(1800, $order->amount());
		$this->assertSame(1800, $order->total());
		$this->assertSame(100 + 300, $order->discount());
	}

	public function test_order_can_be_created_in_db()
	{
		$product_1 = ProductClass::make([
			'id'		=> 1,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 90000,
			'stock'		=> 100,
		]);
		$product_2 = ProductClass::make([
			'id'		=> 2,
			'title' 	=> 'Roadster Men T-Shirt 001',
			'price_mp' 	=> 60000,
			'price_sp' 	=> 45000,
			'stock'		=> 100,
		]);

		$product_1->save();
		$product_2->save();

		$order_item_1 = new OrderItemClass();
		$order_item_2 = new OrderItemClass();

		$order_item_1->addProduct($product_1);
		$order_item_2->addProduct($product_2, $qty = 2);

		$user  = UserClass::find(1)->first();
		$order = new OrderClass();

		$order->_user = $user;
		$order->addOrderItem($order_item_1);
		$order->addOrderItem($order_item_2);


		$this->assertTrue($order->create());
		$this->assertNotNull($db_order = OrderClass::find($order->thisId())->first());

		// Order item is present in the db
		$order_item_1 = OrderItemClass::find($order->orderItems()[0]->thisId())->first();
		$order_item_2 = OrderItemClass::find($order->orderItems()[1]->thisId())->first();
		$this->assertNotNull($order_item_1);
		$this->assertNotNull($order_item_2);

		// Delete
		$order_item_1->delete();
		$order_item_2->delete();
		ProductClass::find($product_1->thisId())->first()->delete();
		ProductClass::find($product_2->thisId())->first()->delete();
		$db_order->delete();
	}

	// @TODO Will think about it later.
	// public function test_order_can_be_created_using_make_and_create_in_db()
	// {
	// 	$order = OrderClass::make([
	// 		'amount' 		=> 1000,
	// 		'sub_total' 	=> 1000,
	// 		'gross_total' 	=> 1000,
	// 		'discount' 		=> 0,
	// 		'tax' 			=> 0,
	// 		'user_id' 		=> 1,
	// 	]);
		
	// 	$this->assertTrue($order->create());
	// 	$this->assertNotNull($db_order = OrderClass::find($order->thisId())->first());

	// 	var_dump($order, $db_order);exit;

	// 	// Delete
	// 	$db_order->delete();
	// }

	public function test_order_can_be_created_using_make_in_db()
	{
		$order = OrderClass::make([
			'amount' 		=> 1000,
			'sub_total' 	=> 1000,
			'gross_total' 	=> 1000,
			'discount' 		=> 0,
			'tax' 			=> 0,
			'user_id' 		=> 1,
		]);
		
		$this->assertTrue($order->save());
		$this->assertNotNull($db_order = OrderClass::find($order->thisId())->first());

		// Delete
		$db_order->delete();
	}

	public function test_order_can_be_loaded_using_id_from_db()
	{
		$product_1 = ProductClass::make([
			'id'		=> 1,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 90000,
			'stock'		=> 100,
		]);
		$product_2 = ProductClass::make([
			'id'		=> 2,
			'title' 	=> 'Roadster Men T-Shirt 001',
			'price_mp' 	=> 60000,
			'price_sp' 	=> 45000,
			'stock'		=> 100,
		]);

		$product_1->save();
		$product_2->save();

		$order_item_1 = new OrderItemClass();
		$order_item_2 = new OrderItemClass();

		$order_item_1->addProduct($product_1);
		$order_item_2->addProduct($product_2, $qty = 2);

		$user  = UserClass::find(1)->first();
		$order = new OrderClass();

		$order->_user = $user;
		$order->addOrderItem($order_item_1);
		$order->addOrderItem($order_item_2);


		$this->assertTrue($order->create());



		// Actual Test for loading of Order
		$loaded_order = OrderClass::load($order_id = (int) $order->thisId());

		$this->assertNotNull($loaded_order);
		// Check order values
		$this->assertSame(2200, $loaded_order->grossTotal());
		$this->assertSame(1800, $loaded_order->subTotal());
		$this->assertSame(1800, $loaded_order->amount());
		$this->assertSame(1800, $loaded_order->total());
		$this->assertSame(100 + 300, $loaded_order->discount());
		
		$this->assertEquals(2, count($order->orderItems()));
		$this->assertSame('Roadster Men Jeans 001', $loaded_order->orderItems()[0]->product()->title);
		$this->assertSame('Roadster Men T-Shirt 001', $loaded_order->orderItems()[1]->product()->title);

		// Delete
		OrderItemClass::find($loaded_order->orderItems()[0]->thisId())->first()->delete();
		OrderItemClass::find($loaded_order->orderItems()[1]->thisId())->first()->delete();
		ProductClass::find($product_1->thisId())->first()->delete();
		ProductClass::find($product_2->thisId())->first()->delete();
		$loaded_order->delete();
	}

	public function test_order_is_loaded_with_correct_details_after_ordered_product_is_updated()
	{
		// Create Product and Order
		$product_1 = ProductClass::make([
			'id'		=> 1,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 90000,
			'stock'		=> 100,
		]);
		$product_2 = ProductClass::make([
			'id'		=> 2,
			'title' 	=> 'Roadster Men T-Shirt 001',
			'price_mp' 	=> 60000,
			'price_sp' 	=> 45000,
			'stock'		=> 100,
		]);

		$product_1->save();
		$product_2->save();

		$order_item_1 = new OrderItemClass();
		$order_item_2 = new OrderItemClass();

		$order_item_1->addProduct($product_1);
		$order_item_2->addProduct($product_2, $qty = 2);

		$user  = UserClass::find(1)->first();
		$order = new OrderClass();

		$order->_user = $user;
		$order->addOrderItem($order_item_1);
		$order->addOrderItem($order_item_2);

		// Order saved in DB
		$this->assertTrue($order->create());


		// After few days admin Updated the product prices
		$product_x = ProductClass::find($product_1->thisId())->first();
		$product_y = ProductClass::find($product_2->thisId())->first();
		$product_x->price_mp = 150000;
		$product_x->price_sp = 100000;
		$product_y->price_mp = 110000;
		$product_y->price_sp = 55000;
		$product_x->save();
		$product_y->save();



		// Actual Test for loading of Order
		$loaded_order = OrderClass::load($order_id = (int) $order->thisId());

		$this->assertNotNull($loaded_order);
		// Check order values
		$this->assertSame(2200, $loaded_order->grossTotal());
		$this->assertSame(1800, $loaded_order->subTotal());
		$this->assertSame(1800, $loaded_order->amount());
		$this->assertSame(1800, $loaded_order->total());
		$this->assertSame(100 + 300, $loaded_order->discount());
		
		$this->assertEquals(2, count($loaded_order->orderItems()));
		$this->assertSame('Roadster Men Jeans 001', $loaded_order->orderItems()[0]->product()->title);
		$this->assertSame('Roadster Men T-Shirt 001', $loaded_order->orderItems()[1]->product()->title);

		$this->assertSame(1000, $loaded_order->orderItems()[0]->markedPrice());
		$this->assertSame(600, $loaded_order->orderItems()[1]->markedPrice());

		// Delete
		OrderItemClass::find($loaded_order->orderItems()[0]->thisId())->first()->delete();
		OrderItemClass::find($loaded_order->orderItems()[1]->thisId())->first()->delete();
		ProductClass::find($product_1->thisId())->first()->delete();
		ProductClass::find($product_2->thisId())->first()->delete();
		$loaded_order->delete();
	}
}