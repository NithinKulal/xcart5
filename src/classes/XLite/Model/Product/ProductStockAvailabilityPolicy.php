<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Product;


use Includes\Utils\ArrayManager;
use Serializable;
use XLite\Model\Cart;
use XLite\Model\Product;

/**
 * ProductStockAvailabilityPolicy encapsulates product availability logic (determines whether the product can be added to cart and what is the available amount).
 *
 * ProductStockAvailabilityPolicy is a serializable (to facilitate caching) class that defines an overridable (see ProductVariants and Wholesale) strategy of product availability calculation. Conceptually, ProductStockAvailabilityPolicy is similar to DDD's concept of value objects, though it is not exactly that.
 *
 * TODO: Can it be integrated into a Product entity as a value object in terms of DDD? Doctrine Embeddable?
 *
 * ProductStockAvailabilityPolicy determines whether the product can be added to a cart or not and what is the available amount. In the most basic scenario, a user can add a product to a cart when the available amount is greater than zero. The available amount, in turn, is defined as a product stock amount minus the amount that the user has already added to cart.
 */
class ProductStockAvailabilityPolicy implements Serializable
{
    const PRODUCT_ID                = 'product_id';
    const PRODUCT_AMOUNT            = 'product_amount';
    const PRODUCT_INVENTORY_ENABLED = 'product_inventory_enabled';
    const PRODUCT_DEFAULT_AMOUNT    = 'product_default_amount';

    /**
     * We call and use this as a data transfer object, though technically it is an array.
     *
     * @var array
     */
    protected $dto;

    /**
     * Construct a new ProductStockAvailabilityPolicy using the given Product instance.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->dto = $this->createDTO($product);
    }

    /**
     * Get product amount available for adding to cart.
     *
     * @param Cart $cart
     *
     * @return int
     */
    public function getAvailableAmount(Cart $cart)
    {
        if ($this->dto[self::PRODUCT_INVENTORY_ENABLED]) {
            $cartItems  = $cart->getItemsByProductId($this->dto[self::PRODUCT_ID]);
            $cartAmount = ArrayManager::sumObjectsArrayFieldValues($cartItems, 'getAmount', true);

            return max(0, $this->dto[self::PRODUCT_AMOUNT] - $cartAmount);
        } else {
            return $this->dto[self::PRODUCT_DEFAULT_AMOUNT];
        }
    }

    /**
     * Check if product is out of stock
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function isOutOfStock(Cart $cart)
    {
        return $this->dto[self::PRODUCT_INVENTORY_ENABLED] && $this->getAvailableAmount($cart) <= 0;
    }

    /**
     * Create a data transfer object out of the Product instance.
     * This object should contain all of the data necessary for getAvailableAmount() & canAddToCart() methods to compute their value.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function createDTO(Product $product)
    {
        return [
            self::PRODUCT_ID                => $product->getProductId(),
            self::PRODUCT_AMOUNT            => $product->getPublicAmount(),
            self::PRODUCT_INVENTORY_ENABLED => $product->getInventoryEnabled(),
            self::PRODUCT_DEFAULT_AMOUNT    => $product->getDefaultAmount(),
        ];
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->dto);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->dto = unserialize($serialized);
    }
}
