<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace XLite\Model\Base;

/**
 * Order item related object interface
 */
interface IOrderItem
{
    /**
     * Get unique id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight();

    /**
     * Get purchase limit (minimum)
     *
     * @return integer
     */
    public function getMinPurchaseLimit();

    /**
     * Get purchase limit (maximum)
     *
     * @return integer
     */
    public function getMaxPurchaseLimit();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku();

    /**
     * Get image
     *
     * @return \XLite\Model\Base\Image|void
     */
    public function getImage();

    /**
     * Get free shipping
     *
     * @return boolean
     */
    public function getFreeShipping();

    /**
     * Get URL
     *
     * @return string
     */
    public function getURL();
}
