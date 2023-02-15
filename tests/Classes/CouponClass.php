<?php

namespace Fantom\Checkout\Tests\Classes;

use Fantom\Checkout\Interfaces\CouponInterface;

/**
 * Coupon Class for testing this library
 */
class CouponClass implements CouponInterface
{
    protected $table = "coupons";
    protected $primary = "id";

    public static function make(array $data = [])
    {
        $c = new self;

        $c->code       = $data['code'];
        $c->value    = (float) $data['value'] * 100;
        $c->created_at   = $c->updated_at = date("Y-m-d H:i:s");

        return $c;
    }

    public function discount(): float
    {
        return (float) $this->value / 100;
    }

    public function getCouponForOrder(int $order_id): CouponInterface
    {
        return $this;
    }






    public function thisId()
    {
        if ($this->lastId()) {
            return $this->lastId();
        }

        return $this->id;
    }
}