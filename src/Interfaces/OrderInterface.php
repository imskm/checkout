<?php

namespace Fantom\Checkout\Interfaces;

use Fantom\Checkout\Interfaces\OrderInterface;
use Fantom\Checkout\Interfaces\OrderItemInterface;

interface OrderInterface
{
	const STATUS_CREATED   = 1;
	const STATUS_PROCESSED = 2;
	const STATUS_FAILED    = 3;
	const STATUS_SUCCESS   = 4;

	public function addOrderItem(OrderItemInterface $order_item);

	public function orderItems();

	/**
	 *
	 * @return double  It will return total() / 100
	 */
	public function amount();

	/**
	 * All theses below methods should calculate in paise
	 * 
	 * @return int
	 */
	public function total();

	public function grossTotal();

	public function subTotal();

	public function discount();

	public function mrpDiscount();

	public function couponDiscount();

	public function tax();

	public function buildOrder(array $data = []): void;

	public static function load(int $order_id): OrderInterface;

	public function getOrderItemsForOrder(int $order_id): array;
}