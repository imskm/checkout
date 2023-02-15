<?php declare(strict_types=1);

namespace Fantom\Checkout\Interfaces;

interface CouponInterface
{
	public function discount(): float;

	public function getCouponForOrder(int $order_id): self;
}