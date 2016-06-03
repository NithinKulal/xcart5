<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\SearchPanel\Countries;
/**
 * Active currencies list
 */
class CurrencyCountries extends \XLite\View\SearchPanel\Countries\Main
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\MultiCurrency\View\Form\Countries\Search';
    }
}