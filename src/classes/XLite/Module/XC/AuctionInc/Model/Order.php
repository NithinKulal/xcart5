<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model;

/**
 * Class represents an order
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * AuctionInc package info
     *
     * @var array
     *
     * @Column (type="array", nullable=true)
     */
    protected $auctionIncPackage = array();

    /**
     * Set auctionIncPackage
     *
     * @param array $auctionIncPackage
     *
     * @return Order
     */
    public function setAuctionIncPackage($auctionIncPackage)
    {
        $this->auctionIncPackage = $auctionIncPackage;
    }

    /**
     * Get auctionIncPackage
     *
     * @return array
     */
    public function getAuctionIncPackage()
    {
        return $this->auctionIncPackage;
    }
}
