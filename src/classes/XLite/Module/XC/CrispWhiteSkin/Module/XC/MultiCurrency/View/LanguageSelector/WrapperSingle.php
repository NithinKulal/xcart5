<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\MultiCurrency\View\LanguageSelector;

/**
 * Language selector (customer) wrapper
 *
 * @Decorator\Depend ("XC\MultiCurrency")
 */
class WrapperSingle extends \XLite\Module\XC\CrispWhiteSkin\View\LanguageSelector\WrapperSingle implements \XLite\Base\IDecorator
{
    /**
     * Check if it is required to be displayed as single(list instead of selectbox)
     *
     * @return bool
     */
    protected function isDisplayAsSingle()
    {
        return false;
    }
}