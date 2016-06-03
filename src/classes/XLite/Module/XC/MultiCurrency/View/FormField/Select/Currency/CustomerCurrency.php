<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select\Currency;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Customer currency selector
 */
class CustomerCurrency extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return MultiCurrency::getInstance()->getSelectedCurrency()->getCode();
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = array();

        $currencies = MultiCurrency::getInstance()->getAvailableCurrencies();

        if (
            is_array($currencies)
            && !empty($currencies)
        ) {
            foreach ($currencies as $currency) {
                $return[$currency->getCurrency()->getCode()] = $currency->getCurrency()->getName();
            }
        }

        return $return;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Currency');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return MultiCurrency::getInstance()->hasMultipleCurrencies();
    }
}