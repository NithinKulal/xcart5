<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Product\Details\Customer;

/**
 * Quantity
 *
 * @Decorator\Depend("XC\ProductVariants")
 * @Decorator\After("CDev\Wholesale")
 */
class ProductVariantQuantity extends \XLite\View\Product\Details\Customer\Quantity implements \XLite\Base\IDecorator
{
    /**
     * Check if the product has wholesale price
     *
     * @return boolean
     */
    protected function hasWholesalePrice()
    {
        return $this->getProductVariant() && !$this->getProductVariant()->getDefaultPrice()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->hasWholesalePrice($this->getProductVariant())
            : parent::hasWholesalePrice();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-wholesale-quantity';
    }
}
