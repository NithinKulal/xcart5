<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\Shipping;

/**
 * Online carrier status
 */
class CarrierStatus extends \XLite\View\Shipping\CarrierStatus implements \XLite\Base\IDecorator
{
    /**
     * Check if method status is switchable
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        $result = parent::isSwitchable();
        if ($result && 'auctionInc' === $this->getMethod()->getProcessor()) {
            $result = \XLite\Module\XC\AuctionInc\Main::isSSAvailable()
                || \XLite\Module\XC\AuctionInc\Main::isXSTrialPeriodValid();
        }

        return $result;
    }
}
