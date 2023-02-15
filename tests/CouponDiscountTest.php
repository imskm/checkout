<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fantom\Checkout\Tests\Classes\OrderClass;
use Fantom\Checkout\Tests\Classes\CouponClass;
use Fantom\Checkout\Tests\Classes\ProductClass;
use Fantom\Checkout\Tests\Classes\OrderItemClass;

/**
 * CouponDiscountTest
 */
class CouponDiscountTest extends TestCase
{
	public function test_coupon_can_be_applied()
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

		// Coupon
		$coupon = CouponClass::make([
			'id' => 1,
			'code' => 'LAUNCH2023',
			'value' => 300,
			'type' => 1,
		]);

		$order->applyCoupon($coupon);

		// Check order values
		$this->assertSame(2200, $order->grossTotal());
		$this->assertSame(1500.0, $order->subTotal());
		$this->assertSame(1500.0, $order->amount());
		$this->assertSame(1500.0, $order->total());
		$this->assertSame(100 + 300 + 300.0, $order->discount());
	}
}