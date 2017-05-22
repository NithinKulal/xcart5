<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Model\Shipping;


/**
 * Shipping rate model
 */
class Rate extends \XLite\Model\Shipping\Rate implements \XLite\Base\IDecorator
{
    /**
     * Base rate value
     *
     * @var float
     */
    protected $freightRate = 0;

    /**
     * Return FreightRate
     *
     * @return float
     */
    public function getFreightRate()
    {
        return $this->freightRate;
    }

    /**
     * Set FreightRate
     *
     * @param float $freightRate
     *
     * @return $this
     */
    public function setFreightRate($freightRate)
    {
        $this->freightRate = $freightRate;
        return $this;
    }

    /**
     * getTotalRate
     *
     * @return float
     */
    public function getTotalRate()
    {
        return parent::getTotalRate() + $this->getFreightRate();
    }
}