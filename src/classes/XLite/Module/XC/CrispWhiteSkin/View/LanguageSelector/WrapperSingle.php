<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\LanguageSelector;

/**
 * Language selector (customer) wrapper
 *
 * @ListChild (list="layout.header.bar.locale.menu", weight="30", zone="customer")
 */
class WrapperSingle extends \XLite\View\LanguageSelector\Customer
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isDisplayAsSingle() ? 'layout/header/locale_menu/language_single.twig' :  'layout/header/locale_menu/language.twig';
    }

    /**
     * Check if it is required to be displayed as single(list instead of selectbox)
     *
     * @return bool
     */
    protected function isDisplayAsSingle()
    {
        return true;
    }
}
