<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\Product\Details\Customer;

/**
 * Quantity
 */
class Quantity extends \XLite\View\Product\Details\Customer\Quantity implements \XLite\Base\IDecorator
{
    /**
     * Define the CSS classes
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return parent::getCSSClass() . ($this->hasWholesalePrice() ? ' wholesale-price-defined' : '');
    }

    /**
     * Check if the product has wholesale price
     *
     * @return boolean
     */
    protected function hasWholesalePrice()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')->hasWholesalePrice(
            $this->getProduct()
        );
    }
}
