# Checkout

A PHP library for complete order checkout system for web application

## Installation

### Using Composer

First in the `composer.json` file add these values:

```json
"repositories": [
	{
		"type": "vcs",
		"url": "https://github.com/imskm/checkout"
	}
],
"require": {
	"imskm/checkout": "^0.1"
}
```

```bash
composer update --no-dev
```

## Integrate the Checkout library with your existing code

Need to add `Trait` and `Interface` in three different Model classes

In the Product class

```php

...

use Fantom\Checkout\Traits\ProductTrait;
use Fantom\Checkout\Interfaces\ProductInterface;


class ProductModel extends Model implements ProductInterface
{
	use ProductTrait;

	// In addition to using ProductTrait you need to also implement
	// few methods as shown below

	public function markedPrice()
    {
        return $this->price_mp / 100;
    }

    public function salePrice()
    {
        return $this->price_sp / 100;
    }

    public function discount()
    {
        return $this->markedPrice() - $this->salePrice();
    }

    public function stock()
    {
        return $this->stock;
    }

    public function tax()
    {
        return 0;

        // @NOTE Inclusive in product pirce

        return $this->salePrice() / 118 * 0.18;
    }
}
```

In the OrderItem class

```php

...

use Fantom\Checkout\Traits\OrderItemTrait;
use Fantom\Checkout\Interfaces\OrderItemInterface;


class OrderItemModel extends Model implements OrderItemInterface
{
	use OrderItemTrait;

	// In addition to using OrderItemTrait you need to also implement this method

	public function getProductFor(int $product_id): ?ProductInterface
    {
        $product = ProductClass::find($product_id);

        if (is_null($product)) {
            return null;
        }

        if ($product->first()) {
            $product = $product->first();
        }

        return $product;
    }
}
```

In the Order class

```php

...

use Fantom\Checkout\Traits\OrderTrait;
use Fantom\Checkout\Interfaces\OrderInterface;


class OrderModel extends Model implements OrderInterface
{
	use OrderTrait;

	// In addition to using OrderTrait you need to also implement this method

	public function getOrderItemsForOrder(int $order_id): array
    {
        return OrderItemClass::load($order_id);
    }
}
```

## Usage

**Read the tests for understanding, how to create and load order**
