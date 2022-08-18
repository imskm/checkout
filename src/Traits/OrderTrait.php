<?php

namespace Fantom\Checkout\Traits;

use Fantom\Checkout\Interfaces\OrderInterface;
use Fantom\Checkout\Interfaces\OrderItemInterface;

/**
 * Order Trait
 */
trait OrderTrait
{
	protected $_order_items = [];
	public $_user;
	public $_error;

	public function create()
	{
		$this->buildOrder();

		if ($this->save() === false) {
			$this->_error = "Failed to save order in database";
			return false;
		}

		// Save order items
		foreach ($this->_order_items as $oi) {
			if ($oi->create($this->_user, $this) === false) {
				$this->_error = $oi->_error;
				return false;
			}
		}

		return true;
	}

	public function addOrderItem(OrderItemInterface $order_item)
	{
		array_push($this->_order_items, $order_item);
	}

	public function orderItems()
	{
		if ($this->_order_items) {
			return $this->_order_items;
		}

		return $this->getOrderItemsForOrder($this->thisId());
	}

	public function amount()
	{
		return $this->total();
	}

	public function total()
	{
		if ($this->_order_items) {
			return $this->calculateOrderTotal();
		}

		return $this->amount / 100;
	}

	public function grossTotal()
	{
		if ($this->_order_items) {
			return $this->sumOrderItemTotalMarkedPrice();
		}

		return $this->gross_total / 100;
	}

	public function subTotal()
	{
		return $this->grossTotal() - $this->discount();
	}

	public function discount()
	{
		if ($this->_order_items) {
			return $this->sumOrderItemTotalDiscount();
		}

		return $this->discount / 100;
	}

	public function tax()
	{
		if ($this->_order_items) {
			return $this->sumOrderItemTotalTax();
		}

		return $this->tax / 100;
	}

	private function sumOrderItemTotalMarkedPrice()
	{
		$sum = 0;
		foreach ($this->_order_items as $oi) {
			$sum += $oi->totalMarkedPrice();
		}

		return $sum;
	}

	private function sumOrderItemTotalDiscount()
	{
		$sum = 0;
		foreach ($this->_order_items as $oi) {
			$sum += $oi->totalDiscount();
		}

		return $sum;
	}

	private function sumOrderItemTotalTax()
	{
		$sum = 0;
		foreach ($this->_order_items as $oi) {
			$sum += $oi->totalTax();
		}

		return $sum;
	}

	private function calculateOrderTotal()
	{
		return $this->subTotal() + $this->tax();
	}

	public function buildOrder(array $data = []): void
	{
		if ($data) {
			$this->amount 		= $data['amount'] * 100;
			$this->sub_total 	= $data['sub_total'] * 100;
			$this->gross_total 	= $data['gross_total'] * 100;
			$this->discount 	= $data['discount'] * 100;
			$this->tax 			= $data['tax'] * 100;
			$this->status 		= OrderInterface::STATUS_CREATED;
			$this->user_id 		= (int) $data['user_id'];
			$this->created_at 	= $this->updated_at = date("Y-m-d H:i:s");
		} else if ($this->_order_items) {
			$this->amount 		= $this->amount() * 100;
			$this->sub_total 	= $this->subTotal() * 100;
			$this->gross_total 	= $this->grossTotal() * 100;
			$this->discount 	= $this->discount() * 100;
			$this->tax 			= $this->tax() * 100;
			$this->status 		= OrderInterface::STATUS_CREATED;
			$this->user_id 		= (int) $this->_user->thisId();
			$this->created_at 	= $this->updated_at = date("Y-m-d H:i:s");
		} else {
			throw new \Exception("No order detail found.");
		}
	}

	public static function load(int $order_id): OrderInterface
	{
		$order = static::find($order_id);
		if (is_null($order)) {
			return null;
		}

		// This is just to avoid ModelOperationTrait::find()
		if (!is_null($order->first())) {
			$order = $order->first();
		}

		foreach ($order->orderItems() as $oi) {
			$order->addOrderItem($oi);
		}

		return $order;
	}
}