<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Controller\Admin;

/**
 * Shipping methods management page controller
 */
class ShippingMethods extends \XLite\Controller\Admin\ShippingMethods implements \XLite\Base\IDecorator
{
    /**
     * Get current country code
     *
     * @return string
     */
    public function getCarrier()
    {
        $carrier = parent::getCarrier();

        return 'auctionInc' !== $carrier || !\XLite\Module\XC\AuctionInc\Main::isSSAvailable()
            ? $carrier
            : 'offline';
    }
}
