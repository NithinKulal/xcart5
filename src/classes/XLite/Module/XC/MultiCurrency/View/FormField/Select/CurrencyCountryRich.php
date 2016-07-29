<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select;

/**
 * Currencies list
 */
class CurrencyCountryRich extends \XLite\View\FormField\Select\Base\Rich
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $countries = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->getActiveCurrencyAvailableCountries(
                \XLite\Core\Request::getInstance()->active_currency_id
            );

        $list = array();
        foreach ($countries as $country) {
            $list[$country->getCode()] = $country->getCountry();
        }

        return $list;
    }
}
