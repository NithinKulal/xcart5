<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Viewer
 *
 * @Decorator\Depend ("CDev\MarketPrice")
 */
abstract class PriceMarketPrice extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Determine if we need to display product market price
     *
     * @return boolean
     */
    protected function isShowMarketPrice()
    {
        return !$this->getProduct()->getParticipateSale() && parent::isShowMarketPrice();
    }
}
