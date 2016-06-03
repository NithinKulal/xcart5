<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Format price
     *
     * @param float                 $value             Price
     * @param \XLite\Model\Currency $currency          Currency OPTIONAL
     * @param boolean               $strictFormat      Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     * @param boolean               $noValueConversion Do not use value conversion OPTIONAL
     *
     * @return string
     */
    public static function formatPrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false, $noValueConversion = false)
    {
        if (!\XLite::isAdminZone()) {
            $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

            if (
                !$noValueConversion
                && isset($selectedCurrency)
                && !$selectedCurrency->isDefaultCurrency()
            ) {
                $value = \XLite\Core\Converter::getInstance()->convertToSelectedMultiCurrency($value);
                $currency = $selectedCurrency->getCurrency();
            }
        }

        return parent::formatPrice($value, $currency, $strictFormat);
    }

    /**
     * Format price as HTML block
     *
     * @param float                 $value             Value
     * @param \XLite\Model\Currency $currency          Currency OPTIONAL
     * @param boolean               $strictFormat      Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     * @param boolean               $noValueConversion Do not use value conversion OPTIONAL
     *
     * @return string
     */
    public function formatPriceHTML($value, \XLite\Model\Currency $currency = null, $strictFormat = false, $noValueConversion = false)
    {
        if (!\XLite::isAdminZone()) {
            $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

            if (
                !$noValueConversion
                && isset($selectedCurrency)
                && !$selectedCurrency->isDefaultCurrency()
            ) {
                $value = \XLite\Core\Converter::getInstance()->convertToSelectedMultiCurrency($value);
                $currency = $selectedCurrency->getCurrency();
            }
        }

        return parent::formatPriceHTML($value, $currency, $strictFormat);
    }

    /**
     * Check if the current currency is default currency
     *
     * @return boolean
     */
    public function isDefaultMultiCurrencySelected()
    {
        return MultiCurrency::getInstance()->isDefaultCurrencySelected();
    }

    /**
     * Add currency to the list of parameters for widget cache
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        if (!\XLite::isAdminZone() && MultiCurrency::getInstance()->getSelectedMultiCurrency()) {
            $params[] = MultiCurrency::getInstance()->getSelectedMultiCurrency()->getActiveCurrencyId();
        }

        return $params;
    }
}
