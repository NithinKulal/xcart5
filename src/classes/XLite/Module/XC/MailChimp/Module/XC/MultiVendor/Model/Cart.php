<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\MultiVendor\Model;

/**
 * Class Cart
 *
 * @Decorator\Depend ("XC\MailChimp")
 * @Decorator\Depend ("XC\MultiVendor")
 */
abstract class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    /**
     * Check if the order needs to send ECommerce360 data
     *
     * @return boolean
     */
    protected function isECommerce360Order()
    {
        return parent::isECommerce360Order()
            && $this->getOrderNumber();
    }
}