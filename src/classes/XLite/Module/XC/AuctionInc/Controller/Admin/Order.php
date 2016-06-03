<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Controller\Admin;

/**
 * Order modify
 */
abstract class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if ($this->getAuctionIncPackage()) {
            $list['auctionIncPackage'] = static::t('ShippingCalc Package Data');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        if ($this->getAuctionIncPackage()) {
            $list += array(
                'auctionIncPackage' => 'modules/XC/AuctionInc/order/package.twig',
            );
        }

        return $list;
    }

    /**
     * Get package data
     *
     * @return array
     */
    public function getAuctionIncPackage()
    {
        return $this->getOrder() && count($this->getOrder()->getAuctionIncPackage())
            ? $this->getOrder()->getAuctionIncPackage()
            : array();
    }
}
