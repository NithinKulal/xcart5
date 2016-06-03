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
 * @ListChild (list="layout.header.bar", weight="80", zone="customer")
 */
class LocaleIndicator extends \XLite\View\LanguageSelector\Customer
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/header.bar.locale.twig';
    }
}
