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
class CurrencyRich extends \XLite\View\FormField\Select\CurrencyRich
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        $currencies = \XLite\Core\Database::getRepo('XLite\Model\Currency')->getAllInactiveCurrencies();
        foreach ($currencies as $currency) {
            $list[$currency->getCurrencyId()] = $this->getOptionName($currency);
        }

        return $list;
    }

    /**
     * Returns option name
     *
     * @param \XLite\Model\Currency $currency Currency
     *
     * @return string
     */
    protected function getOptionName($currency)
    {
        return sprintf('%s - %s', $currency->getCode(), $currency->getName());
    }
}
