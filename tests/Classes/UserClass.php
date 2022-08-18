<?php

namespace Fantom\Checkout\Tests\Classes;

use Fantom\Database\Model;

/**
 * UserClass
 */
class UserClass extends Model
{
	protected $table = "users";
	protected $primary = "id";

	public function thisId()
    {
        if ($this->lastId()) {
            return $this->lastId();
        }

        return $this->id;
    }
}