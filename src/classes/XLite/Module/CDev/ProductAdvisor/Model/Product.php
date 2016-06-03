<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Model;

/**
 * Product model extension
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Relation to product views statistics
     *
     * @var   \XLite\Module\CDev\ProductAdvisor\Model\ProductStats
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\ProductAdvisor\Model\ProductStats", mappedBy="viewed_product", fetch="LAZY")
     */
    protected $views_stats;

    /**
     * Relation to product purchase statistics
     *
     * @var   \XLite\Module\CDev\ProductAdvisor\Model\ProductStats
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\ProductAdvisor\Model\ProductStats", mappedBy="bought_product", fetch="LAZY")
     */
    protected $purchase_stats;


    /**
     * Returns true if product is classified as a new product
     * 
     * @return boolean
     */
    public function isNewProduct()
    {
        $currentDate = static::getUserTime();

        $daysOffset = \XLite\Module\CDev\ProductAdvisor\Main::getNewArrivalsOffset();

        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_enabled
            && $this->getArrivalDate() 
            && $this->getArrivalDate() < $currentDate 
            && $this->getArrivalDate() > $currentDate - 86400 * $daysOffset;
    }

    /**
     * Returns true if product is classified as an upcoming product
     * 
     * @return boolean
     */
    public function isUpcomingProduct()
    {
        $currentDate = static::getUserTime();

        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled
            && $this->getArrivalDate() 
            && $this->getArrivalDate() > $currentDate;
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->isUpcomingProduct()
            ? false
            : parent::isShowStockWarning();
    }

    /**
     * Add views_stats
     *
     * @param \XLite\Module\CDev\ProductAdvisor\Model\ProductStats $viewsStats
     * @return Product
     */
    public function addViewsStats(\XLite\Module\CDev\ProductAdvisor\Model\ProductStats $viewsStats)
    {
        $this->views_stats[] = $viewsStats;
        return $this;
    }

    /**
     * Get views_stats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getViewsStats()
    {
        return $this->views_stats;
    }

    /**
     * Add purchase_stats
     *
     * @param \XLite\Module\CDev\ProductAdvisor\Model\ProductStats $purchaseStats
     * @return Product
     */
    public function addPurchaseStats(\XLite\Module\CDev\ProductAdvisor\Model\ProductStats $purchaseStats)
    {
        $this->purchase_stats[] = $purchaseStats;
        return $this;
    }

    /**
     * Get purchase_stats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPurchaseStats()
    {
        return $this->purchase_stats;
    }
}
