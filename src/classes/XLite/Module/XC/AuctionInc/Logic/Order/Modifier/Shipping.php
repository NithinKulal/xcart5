<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Logic\Order\Modifier;

/**
 * Shipping modifier
 */
class Shipping extends \XLite\Logic\Order\Modifier\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Set shipping rate and shipping method id
     *
     * @param \XLite\Model\Shipping\Rate $rate Shipping rate object OPTIONAL
     *
     * @return void
     */
    public function setSelectedRate(\XLite\Model\Shipping\Rate $rate = null)
    {
        parent::setSelectedRate($rate);

        $package = $rate && $rate->getExtraData() && $rate->getExtraData()->auctionIncPackage
            ? $rate->getExtraData()->auctionIncPackage
            : array();

        $this->order->setAuctionIncPackage($package);
    }

    /**
     * Restore rates
     *
     * @return array(\XLite\Model\Shipping\Rate)
     */
    protected function restoreRates()
    {
        $rates = parent::restoreRates();

        if ($rates && $this->order->getAuctionIncPackage()) {
            $extraData = new \XLite\Core\CommonCell(array('auctionIncPackage' => $this->order->getAuctionIncPackage()));
            $rates[0]->setExtraData($extraData);
        }

        return $rates;
    }

    /**
     * Shipping rates sorting callback
     *
     * @param \XLite\Model\Shipping\Rate $a First shipping rate
     * @param \XLite\Model\Shipping\Rate $b Second shipping rate
     *
     * @return integer
     */
    protected function compareRates(\XLite\Model\Shipping\Rate $a, \XLite\Model\Shipping\Rate $b)
    {
        $aMethod = $a->getMethod();
        $bMethod = $b->getMethod();

        $aRate = $a->getTotalRate();
        $bRate = $b->getTotalRate();

        return 'auctionInc' === $aMethod->getProcessor() && 'auctionInc' === $bMethod->getProcessor()
            ? ($aRate === $bRate ? 0 : ($aRate < $bRate ? -1 : 1))
            : parent::compareRates($a, $b);
    }
}
