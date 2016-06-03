<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

/**
 * Shipping rate model
 */
class Rate extends \XLite\Base\SuperClass
{
    /**
     * Shipping method object
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $method;

    /**
     * Shipping markup object
     *
     * @var \XLite\Model\Shipping\Markup
     */
    protected $markup;

    /**
     * Base rate value
     *
     * @var float
     */
    protected $baseRate = 0;

    /**
     * Markup rate value
     *
     * @var float
     */
    protected $markupRate = 0;

    /**
     * Rate's extra data (real-time rate calculation's details)
     *
     * @var \XLite\Core\CommonCell
     */
    protected $extraData;

    /**
     * Public class constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * getMethod
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setMethod
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method object
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * getMarkup
     *
     * @return \XLite\Model\Shipping\Markup
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * setMarkup
     *
     * @param \XLite\Model\Shipping\Markup $markup Shipping markup object
     *
     * @return void
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;
    }

    /**
     * getBaseRate
     *
     * @return float
     */
    public function getBaseRate()
    {
        return $this->baseRate;
    }

    /**
     * setBaseRate
     *
     * @param float $baseRate Base rate value
     *
     * @return void
     */
    public function setBaseRate($baseRate)
    {
        $this->baseRate = $baseRate;
    }

    /**
     * getMarkupRate
     *
     * @return float
     */
    public function getMarkupRate()
    {
        $handlingFee = \XLite::isFreeLicense()
            ? 0
            : $this->getMethod()->getHandlingFee();

        return $this->markupRate + $handlingFee;
    }

    /**
     * setMarkupRate
     *
     * @param float $markupRate Markup rate value
     *
     * @return void
     */
    public function setMarkupRate($markupRate)
    {
        $this->markupRate = $markupRate;
    }

    /**
     * getExtraData
     *
     * @return \XLite\Core\CommonCell
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * setExtraData
     *
     * @param \XLite\Core\CommonCell $extraData Rate's extra data
     *
     * @return void
     */
    public function setExtraData(\XLite\Core\CommonCell $extraData)
    {
        $this->extraData = $extraData;
    }

    /**
     * getTotalRate
     *
     * @return float
     */
    public function getTotalRate()
    {
        return $this->getBaseRate() + $this->getMarkupRate();
    }

    /**
     * Get taxable basis
     *
     * @return float
     */
    public function getTaxableBasis()
    {
        return $this->getBaseRate() + $this->getMarkupRate();
    }

    /**
     * getMethodId
     *
     * @return integer
     */
    public function getMethodId()
    {
        return $this->getMethod() ? $this->getMethod()->getMethodId() : null;
    }

    /**
     * getMethodName
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->getMethod() ? $this->getMethod()->getName() : null;
    }
}
