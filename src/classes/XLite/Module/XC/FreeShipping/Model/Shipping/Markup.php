<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Model\Shipping;

/**
 * Shipping markup model
 */
class Markup extends \XLite\Model\Shipping\Markup implements \XLite\Base\IDecorator
{
    /**
     * Has rates
     *
     * @return boolean
     */
    public function hasRates()
    {
        return !$this->getShippingMethod()
            || !$this->getShippingMethod()->getFree();
    }
}
