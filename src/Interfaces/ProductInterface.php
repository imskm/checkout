<?php

namespace Fantom\Checkout\Interfaces;

interface ProductInterface
{
	public function markedPrice();

	public function salePrice();

	public function discount();

	public function stock();
}