<?php

namespace Fantom\Checkout\Traits;

use Fantom\Checkout\Interfaces\ProductInterface;
use Fantom\Checkout\Interfaces\OrderItemInterface;

trait OrderItemTrait
{
	protected $_product;
	protected $_qty;

	public function create($user, $order)
	{
		$this->buildOrderItem([], $user, $order);

		return $this->save();
	}

	public function addProduct(ProductInterface $product, int $qty = 1)
	{
		$this->_product = $product;
		$this->_qty     = $qty;
	}

	public function product()
	{
		if (!is_null($this->_product)) {
			return $this->_product;
		}

		// Otherwise fetch the product from DB and cache it
		return $this->getProductFor((int) $this->product_id);
	}

	public function qty(?int $qty = null)
	{
		if (!is_null($qty)) {
			$this->_qty = $qty;
		} elseif (is_null($this->_product)) {
			return $this->qty;
		}

		return $this->_qty;;
	}

	public function markedPrice()
	{
		return $this->product()->markedPrice();
	}

	public function salePrice()
	{
		return $this->product()->salePrice();
	}

	public function discount()
	{
		return $this->product()->discount();
	}

	public function totalMarkedPrice()
	{
		return $this->markedPrice() * $this->qty();
	}

	public function totalSalePrice()
	{
		return $this->salePrice() * $this->qty();
	}

	public function totalDiscount()
	{
		return $this->discount() * $this->qty();
	}

	public function totalTax()
	{
		return $this->product()->tax() * $this->qty();
	}

	public function buildOrderItem(array $data = [], $user = null, $order = null)
	{
		if ($data) {
			$this->order_id 	= (int) $data['order_id'];
			$this->product_id 	= (int) $data['product_id'];
			$this->user_id 		= (int) $data['user_id'];
			$this->qty 			= (int) $data['qty'];
			$this->price_mp 	= (int) $data['price_mp'] * 100;
			$this->price_sp 	= (int) $data['price_sp'] * 100;
			$this->discount 	= (int) $data['discount'] * 100;
			$this->status 		= OrderItemInterface::STATUS_CREATED;
		} else if ($this->_product) {
			$this->order_id 	= (int) $order->thisId();
			$this->product_id 	= (int) $this->_product->thisId();
			$this->user_id 		= (int) $user->thisId();
			$this->qty 			= $this->qty();
			$this->price_mp 	= $this->markedPrice() * 100;
			$this->price_sp 	= $this->salePrice() * 100;
			$this->discount 	= $this->discount() * 100;
			$this->status 		= OrderItemInterface::STATUS_CREATED;
		} else {
			throw new \Exception("Order item detail not found.");
		}
	}

	public function hasStock(): bool
	{
		return $this->product()->stock() >= $this->qty();
	}

	public static function load(int $order_id): array
	{
		$order_items = static::where('order_id', $order_id)->get();

		foreach ($order_items as $oi) {
			$oi->addProduct($oi->product(), $oi->qty());
		}

		return $order_items;
	}
}
