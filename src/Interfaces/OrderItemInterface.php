<?php

namespace Fantom\Checkout\Interfaces;

use Fantom\Checkout\Interfaces\ProductInterface;

interface OrderItemInterface
{
	const STATUS_CREATED 				=   1;
	const STATUS_CANCELLATION_REQUESTED =   2;
	const STATUS_CANCELLED              =   4;  
	const STATUS_SHIPPED                =   8;  
	const STATUS_DELIVERED              =  16; 
	const STATUS_RETURN_REQUESTED       =  32; 
	const STATUS_RETURNED               =  64; 
	const STATUS_REFUND_REQUESTED       = 128;
	const STATUS_REFUNDED               = 256;
	const STATUS_UNDELIVERED			= 512;

	public function addProduct(ProductInterface $product, int $qty = 1);

	public function product();

	public function qty(?int $qty = null);

	public function markedPrice();

	public function salePrice();

	public function discount();

	/**
	 * @return price_xx * qty
	 */
	public function totalMarkedPrice();

	public function totalSalePrice();

	public function totalDiscount();

	public function hasStock(): bool;

	/**
	 * @param int   $product_id
	 * 
	 * @return array   Array of OrderItem class 
	 */
	public static function load(int $order_id): array;

	public function getProductFor(int $product_id): ?ProductInterface;
}