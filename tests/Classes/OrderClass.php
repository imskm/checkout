<?php

namespace Fantom\Checkout\Tests\Classes;

use Fantom\Database\Model;
use Fantom\Checkout\Traits\OrderTrait;
use Fantom\Checkout\Interfaces\OrderInterface;
use Fantom\Checkout\Tests\Classes\OrderItemClass;
use Fantom\Checkout\Interfaces\OrderItemInterface;

/**
 * Order Class for testing this library
 */
class OrderClass extends Model implements OrderInterface
{
    protected $table = "orders";
    protected $primary = "id";

	use OrderTrait;

    public static function make(array $data = [])
    {
        $o = new self;

        $o->amount       = $data['amount'] * 100;
        $o->sub_total    = $data['sub_total'] * 100;
        $o->gross_total  = $data['gross_total'] * 100;
        $o->discount     = $data['discount'] * 100;
        $o->tax          = $data['tax'] * 100;
        $o->status       = OrderInterface::STATUS_SUCCESS;
        $o->user_id      = (int) $data['user_id'];
        $o->created_at   = $o->updated_at = date("Y-m-d H:i:s");

        return $o;
    }

    public function getOrderItemsForOrder(int $order_id): array
    {
        return OrderItemClass::load($order_id);
    }




    public function thisId()
    {
        if ($this->lastId()) {
            return $this->lastId();
        }

        return $this->id;
    }
}