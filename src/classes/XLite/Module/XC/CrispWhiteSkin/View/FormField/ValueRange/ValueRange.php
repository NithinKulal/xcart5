<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\FormField\ValueRange;

/**
 * Value range
 *
 * @Decorator\Depend("XC\ProductFilter")
 */
class ValueRange extends \XLite\Module\XC\ProductFilter\View\FormField\ValueRange\ValueRange implements \XLite\Base\IDecorator
{
    /**
     * Get value container class
     *
     * @return string
     */
    protected function getCommonClass()
    {
        return '';
    }
}
