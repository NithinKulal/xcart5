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
 */
class RealChargeWarning extends \XLite\View\AView
{
    const PARAM_ORDER = 'order';

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['css'][] = [
            'file'      => $this->getDir() . LC_DS . 'real_charge_style.css'
        ];

        return $list;
    }
    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                '\XLite\Model\Cart'
            )
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'real_charge_warning.twig';
    }

    /**
     * Get directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules' . LC_DS . 'XC' . LC_DS . 'MultiCurrency' . LC_DS . 'shopping_cart';
    }

    /**
     * Get note
     *
     * @return string
     */
    protected function getSelectedRateText()
    {
        return static::t(
            'Note: All prices billed in {{currency}}. Current exchange rate is {{exchange_rate}}.',
            array(
                'currency'      => $this->getDefaultCurrencyText(
                    MultiCurrency::getInstance()->getDefaultCurrency()
                ),
                'exchange_rate' => $this->getSelectedCurrencyRateText(
                    MultiCurrency::getInstance()->getSelectedCurrency(),
                    MultiCurrency::getInstance()->getSelectedCurrency()->getActiveCurrency()->getRate(),
                    MultiCurrency::getInstance()->getDefaultCurrency()
                )
            )
        );
    }

    /**
     * Get default currency text
     *
     * @param \XLite\Model\Currency $defaultCurrency Currency
     *
     * @return string
     */
    protected function getDefaultCurrencyText(\XLite\Model\Currency $defaultCurrency)
    {
        $prefix = $defaultCurrency->getPrefix();

        $prefix = empty($prefix) ? $defaultCurrency->getSuffix() : $prefix;
        $prefix = empty($prefix) ? '' : ' (' . $prefix . ')';

        return $defaultCurrency->getCode() . $prefix;
    }

    /**
     * Get selected currency text
     *
     * @param \XLite\Model\Currency $selectedCurrency Selected currency
     * @param float                 $rate             Rate
     * @param \XLite\Model\Currency $defaultCurrency  Default currency
     *
     * @return string
     */
    protected function getSelectedCurrencyRateText(\XLite\Model\Currency $selectedCurrency, $rate, \XLite\Model\Currency $defaultCurrency)
    {
        $rate = 1 / $rate;

        $precision = $defaultCurrency->getE();

        $defaultCurrency->setE(4);

        $return = $this->formatPrice(1, $selectedCurrency, false, true)
            . ' = ' . $this->formatPrice($rate, $defaultCurrency, false, true);

        $defaultCurrency->setE($precision);

        return $return;
    }
}
