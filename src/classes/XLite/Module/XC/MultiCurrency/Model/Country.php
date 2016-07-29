<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model;

/**
 * Country
 *
 * @Table (indexes={
 *      @Index (name="active_currency", columns={"active_currency"})
 *  }
 * )
 *
 * @MappedSuperclass
 */
class Country extends \XLite\Model\Country implements \XLite\Base\IDecorator
{
    /**
     * Active currencies
     *
     * @var \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     *
     * @ManyToOne (targetEntity="XLite\Module\XC\MultiCurrency\Model\ActiveCurrency", inversedBy="countries")
     * @JoinColumn (name="active_currency", referencedColumnName="active_currency_id", onDelete="SET NULL")
     */
    protected $active_currency;

    /**
     * Check if country has assigned currencies
     *
     * @return boolean
     */
    public function hasAssignedCurrencies()
    {
        return $this->getRepository()->hasAssignedCurrencies($this);
    }

    /**
     * Set active_currency
     *
     * @param \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency $activeCurrency
     * @return Country
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