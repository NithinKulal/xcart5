<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Core;

/**
 * CMSConnector class
 */
abstract class CMSConnector extends \XLite\Core\CMSConnector implements \XLite\Base\IDecorator
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        $this->widgetsList['\XLite\Module\CDev\ProductAdvisor\View\NewArrivals'] = 'New arrivals';
        $this->widgetsList['\XLite\Module\CDev\ProductAdvisor\View\ComingSoon'] = 'Coming soon';
        $this->widgetsList['\XLite\Module\CDev\ProductAdvisor\View\RecentlyViewed'] = 'Recently viewed';
        $this->widgetsList['\XLite\Module\CDev\ProductAdvisor\View\BoughtBought'] = 'Customers who bought this product also bought';
        $this->widgetsList['\XLite\Module\CDev\ProductAdvisor\View\ViewedBought'] = 'Customers who viewed this product bought';
    }
}
