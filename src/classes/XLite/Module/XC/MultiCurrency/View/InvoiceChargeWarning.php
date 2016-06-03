<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Product price
 *
 *
 */
class InvoiceChargeWarning extends \XLite\Module\XC\MultiCurrency\View\RealChargeWarning
{
    /**
     * Get note
     *
     * @return string
     */
    protected function getSelectedRateText()
    {
        $order = $this->getOrder();

        if (
            isset($order)
            && $order->isMultiCurrencyOrder()
        ) {
            $return = static::t(
                'Note: Order billed in {{currency}}. Exchange rate is {{exchange_rate}}.',
                array(
                    'currency' => $this->getDefaultCurrencyText(
                        $order->getCurrency()
                    ),
                    'exchange_rate' => $this->getSelectedCurrencyRateText(
                        $order->getSelectedMultiCurrency(),
                        $order->getSelectedMultiCurrencyRate(),
                        $order->getCurrency()
                    )
                )
            );
        } else {
            $return = '';
        }

        return $return;
    }
}