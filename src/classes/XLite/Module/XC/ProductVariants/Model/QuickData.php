<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

/**
 * Quick data
 */
class QuickData extends \XLite\Model\QuickData implements \XLite\Base\IDecorator
{
    /**
     * Minimal price
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $minPrice = 0.0000;

    /**
     * Maximal price
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $maxPrice = 0.0000;

    /**
     * Return MinPrice
     *
     * @return float
     */
    public function getMinPrice()
    {
        return $this->minPrice;
    }

    /**
     * Set MinPrice
     *
     * @param float $minPrice
     *
     * @return $this
     */
    public function setMinPrice($minPrice)
    {
        $this->minPrice = $minPrice;
        return $this;
    }

    /**
     * Return MaxPrice
     *
     * @return float
     */
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * Set MaxPrice
     *
     * @param float $maxPrice
     *
     * @return $this
     */
    public function setMaxPrice($maxPrice)
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }
}