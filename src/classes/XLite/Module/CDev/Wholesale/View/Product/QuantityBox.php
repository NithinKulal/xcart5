<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Product;

/**
 * Quantity box
 */
class QuantityBox extends \XLite\View\Product\QuantityBox implements \XLite\Base\IDecorator
{
    /**
     * Return minimum quantity
     *
     * @return integer
     */
    protected function getMinQuantity()
    {
        $minQuantity = $this->getProduct()->getMinQuantity(
            $this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null
        );

        $result = parent::getMinQuantity();

        $minimumQuantity = $minQuantity ? $minQuantity : $result;

        if (!$this->isCartPage()) {

            $items = \XLite\Model\Cart::getInstance()->getItemsByProductId($this->getProduct()->getProductId());

            $quantityInCart = $items
                ? \Includes\Utils\ArrayManager::sumObjectsArrayFieldValues(
                    $items,
                    'getAmount',
                    true
                )
                : 0;

            $result = ($minimumQuantity > $quantityInCart) ? ($minimumQuantity - $quantityInCart) : $result;

        } else {

            $result = $minimumQuantity;
        }

        return $result;
    }
}
