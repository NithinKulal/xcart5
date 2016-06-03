<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Currency country state switcher
 */
class CurrencyCountry extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\Enabled
{
    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        $activeCountries = array();

        if (!empty(\XLite\Core\Request::getInstance()->active_currency_id)) {
            $activeCountries = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->getActiveCountriesIds(\XLite\Core\Request::getInstance()->active_currency_id);
        }

        return in_array($this->getEntity()->getId(), $activeCountries);
    }
}