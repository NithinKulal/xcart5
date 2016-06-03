<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\AddToCompare;

/**
 * Product comparison widget
 *
 * @Decorator\Depend("XC\ProductComparison")
 */
class ProductCompareLink extends \XLite\Module\XC\ProductComparison\View\AddToCompare\ProductCompareLink implements \XLite\Base\IDecorator
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ProductComparison/header_settings_link.twig';
    }
}
