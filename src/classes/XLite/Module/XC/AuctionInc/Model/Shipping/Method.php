<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model\Shipping;

/**
 * Shipping method model
 */
class Method extends \XLite\Model\Shipping\Method implements \XLite\Base\IDecorator
{
    /**
     * On-demand flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $onDemand = false;

    /**
     * Set onDemand
     *
     * @param boolean $onDemand
     * @return Method
     */
    public function setOnDemand($onDemand)
    {
        $this->onDemand = $onDemand;
        return $this;
    }

    /**
     * Get onDemand
     *
     * @return boolean 
     */
    public function getOnDemand()
    {
        return $this->onDemand;
    }
}
