<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Fantom\Checkout\Tests\Classes\ProductClass;
use Fantom\Checkout\Interfaces\ProductInterface;

/**
 * ProductTest
 */
class ProductTest extends TestCase
{
	private $attributes = [
		'id'		=> 1,
		'title' 	=> 'Roadster Men Jeans 001',
		'price_mp' 	=> 100000,
		'price_sp' 	=> 90000,
		'stock'		=> 100,
	];

	public function test_product_object_can_be_created()
	{
		$this->assertInstanceOf(
			ProductInterface::class,
			ProductClass::make($this->attributes)
		);
	}

	public function test_product_details_can_be_fetched_correctly()
	{
		$product = ProductClass::make($this->attributes);

		$this->assertEquals(1000, $product->markedPrice());
		$this->assertEquals(900, $product->salePrice());
		$this->assertEquals(100, $product->discount());
	}

	public function test_discount_is_calculated_correctly()
	{
		$product = ProductClass::make([
			'id'		=> 111,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 100000,
			'stock'		=> 100,
		]);

		$this->assertEquals(0, $product->discount());
	}

	public function test_product_can_be_created_in_db()
	{
		$product = ProductClass::make([
			'id'		=> 111,
			'title' 	=> 'Roadster Men Jeans 001',
			'price_mp' 	=> 100000,
			'price_sp' 	=> 100000,
			'stock'		=> 100,
		]);

		$this->assertTrue($product->save());

		$db_product = ProductClass::find($product->lastId())->first();

		$this->assertNotNull($db_product);
		$this->assertInstanceOf(ProductClass::class, $db_product);
		$db_product->delete();
	}
}