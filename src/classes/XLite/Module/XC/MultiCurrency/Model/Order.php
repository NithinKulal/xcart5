<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Class represents an order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Selected multi currency
     *
     * @var \XLite\Model\Currency
     *
     * @OneToOne (targetEntity="XLite\Model\Currency")
     * @JoinColumn (name="multi_currency_id", referencedColumnName="currency_id")
     */
    protected $selectedMultiCurrency = null;

    /**
     * Selected multi currency rate
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $selectedMultiCurrencyRate = 1.0;

    /**
     * Since Doctrine lifecycle callbacks do not allow to modify associations, we've added this method
     *
     * @param string $type Type of current operation
     *
     * @return void
     */
    public function prepareEntityBeforeCommit($type)
    {
        if (
            static::ACTION_UPDATE == $type
            && !isset($this->selectedMultiCurrency)
        ) {
            $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

            if (
                isset($selectedCurrency)
                && !$selectedCurrency->isDefaultCurrency()
            ) {
                $this->setSelectedMultiCurrency($selectedCurrency->getCurrency());
                $this->setSelectedMultiCurrencyRate($selectedCurrency->getRate());
            }
        }

        parent::prepareEntityBeforeCommit($type);
    }

    /**
     * Check if order is multi currency order (display currency is different from charge currency)
     *
     * @return boolean
     */
    public function isMultiCurrencyOrder()
    {
        $return = false;

        $orderCurrency = $this->getCurrency();
        $selectedCurrency = $this->getSelectedMultiCurrency();

        if (
            isset($selectedCurrency)
            && $orderCurrency->getCode() != $selectedCurrency->getCode()
        ) {
            $return = true;
        }

        return $return;
    }

    /**
     * Set selectedMultiCurrencyRate
     *
     * @param decimal $selectedMultiCurrencyRate
     * @return Order
     */
    public function setSelectedMultiCurrencyRate($selectedMultiCurrencyRate)
    {
        $this->selectedMultiCurrencyRate = $selectedMultiCurrencyRate;
        return $this;
    }

    /**
     * Get selectedMultiCurrencyRate
     *
     * @return decimal 
     */
    public function getSelectedMultiCurrencyRate()
    {
        return $this->selectedMultiCurrencyRate;
    }

    /**
     * Set selectedMultiCurrency
     *
     * @param \XLite\Model\Currency $selectedMultiCurrency
     * @return Order
     */
    public function setSelectedMultiCurrency(\XLite\Model\Currency $selectedMultiCurrency = null)
    {
        $this->selectedMultiCurrency = $selectedMultiCurrency;
        return $this;
    }

    /**
     * Get selectedMultiCurrency
     *
     * @return \XLite\Model\Currency 
     */
    public function getSelectedMultiCurrency()
    {
        return $this->selectedMultiCurrency;
    }
}