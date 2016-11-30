<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\ProductComparison\View;
use XLite\Module\XC\ProductComparison\Core\Data;

/**
 * Product comparison widget
 *
 * @Decorator\Depend ("XC\ProductComparison")
 */
class HeaderSettings extends \XLite\Module\XC\CrispWhiteSkin\View\HeaderSettings implements \XLite\Base\IDecorator
{
    /**
     * Check if recently updated
     *
     * @return bool
     */
    protected function isRecentlyUpdated()
    {
        return parent::isRecentlyUpdated() || (Data::getInstance()->getProductsCount() > 0 && Data::getInstance()->isRecentlyUpdated());
    }
}