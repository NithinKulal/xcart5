<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

/**
 * Wholesale prices for product variant
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariantPrice extends \XLite\Module\CDev\Wholesale\View\ProductPrice implements \XLite\Base\IDecorator
{
    /**
     * Define wholesale prices
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function defineWholesalePrices()
    {
        return $this->getProductVariant() && !$this->getProductVariant()->getDefaultPrice()
            ? \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->getWholesalePrices(
                $this->getProductVariant(),
                $this->getCart()->getProfile() ? $this->getCart()->getProfile()->getMembership() : null
            )
            : parent::defineWholesalePrices();
    }
}
