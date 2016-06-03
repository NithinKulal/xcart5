<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Invoice widget
 */
class Invoice extends \XLite\View\Invoice implements \XLite\Base\IDecorator
{
    /**
     * Format price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     *
     * @return string
     */
    protected function formatInvoicePrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if ($this->isMultiCurrencyOrder()) {
            $value = MultiCurrency::getInstance()->convertValueByRate(
                $value,
                $this->getOrder()->getSelectedMultiCurrencyRate()
            );

            $currency = $this->getOrder()->getSelectedMultiCurrency();
        }

        return static::formatPrice($value, $currency, $strictFormat, true);
    }

    /**
     * Format surcharge value
     *
     * @param array $surcharge Surcharge
     *
     * @return string
     */
    protected function formatSurcharge(array $surcharge)
    {
        if ($this->isMultiCurrencyOrder()) {
            $value = MultiCurrency::getInstance()->convertValueByRate(
                abs($surcharge['cost']),
                $this->getOrder()->getSelectedMultiCurrencyRate()
            );

            $return = $this->formatPrice(
                $value,
                $this->getOrder()->getSelectedMultiCurrency(),
                !\XLite::isAdminZone(),
                true
            );
        } else {
            $return = $this->formatPrice(
                abs($surcharge['cost']),
                $this->getOrder()->getCurrency(),
                !\XLite::isAdminZone(),
                true
            );
        }

        return $return;
    }

    /**
     * Check if current widget order is multi currency order (display currency is different from charge currency)
     *
     * @return boolean
     */
    protected function isMultiCurrencyOrder()
    {
        $return = false;

        $order = $this->getOrder();

        if (isset($order)) {
            $return = $order->isMultiCurrencyOrder();
        }

        return $return;
    }
}