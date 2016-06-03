<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model;

/**
 * The "product" model class
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * AuctionInc related data
     *
     * @var \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc
     *
     * @OneToOne (
     *     targetEntity="XLite\Module\XC\AuctionInc\Model\ProductAuctionInc",
     *     mappedBy="product",
     *     fetch="LAZY",
     *     cascade={"all"}
     * )
     */
    protected $auctionIncData;

    /**
     * Set auctionIncData
     *
     * @param \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc $auctionIncData
     * @return Product
     */
    public function setAuctionIncData(\XLite\Module\XC\AuctionInc\Model\ProductAuctionInc $auctionIncData = null)
    {
        $this->auctionIncData = $auctionIncData;
        return $this;
    }

    /**
     * Get auctionIncData
     *
     * @return \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc 
     */
    public function getAuctionIncData()
    {
        return $this->auctionIncData;
    }
}
