<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model;

/**
 * Currency
 */
class Currency extends \XLite\Model\Currency implements \XLite\Base\IDecorator
{
    /**
     * Active currency
     *
     * @var \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     *
     * @OneToOne (targetEntity="XLite\Module\XC\MultiCurrency\Model\ActiveCurrency", mappedBy="currency")
     */
    protected $active_currency;

    /**
     * Check if currency is active multicurrency
     *
     * @return boolean
     */
    public function isActiveMultiCurrency()
    {
        $activeCurrency = $this->getActiveCurrency();

        return isset($activeCurrency) ? true : false;
    }

    /**
     * Set active_currency
     *
     * @param \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $activeCurrency
     * @return Currency
     */
    public function setActiveCurrency(\XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $activeCurrency = null)
    {
        $this->active_currency = $activeCurrency;
        return $this;
    }

    /**
     * Get active_currency
     *
     * @return \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency 
     */
    public function getActiveCurrency()
    {
        return $this->active_currency;
    }
}