<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model;

/**
 * Class represents an order item model
 */
abstract class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Canada Post parcel items (reference to the Canada Post parcel item model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item", mappedBy="orderItem", cascade={"all"})
     */
    protected $capostParcelItems;

    /**
     * Canada Post return items (reference to the Canada Post return item model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item", mappedBy="orderItem", cascade={"all"})
     */
    protected $capostReturnItems;

    /**
     * Add a Canada Post parcel item 
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $newItem Parcel's item model
     *
     * @return void
     */
    public function addCapostParcelItem(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $newItem)
    {
        $newItem->setOrderItem($this);

        $this->addCapostParcelItems($newItem);
    }

    /**
     * Add a Canada Post return item
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $newItem Retrun's item model
     *
     * @return void
     */
    public function addCapostReturnItem(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $newItem)
    {
        $newItem->setOrderItem($this);
        
        $this->addCapostReturnItems($newItem);
    }

    /**
     * Add capostParcelItems
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $capostParcelItems
     * @return OrderItem
     */
    public function addCapostParcelItems(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $capostParcelItems)
    {
        $this->capostParcelItems[] = $capostParcelItems;
        return $this;
    }

    /**
     * Get capostParcelItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCapostParcelItems()
    {
        return $this->capostParcelItems;
    }

    /**
     * Add capostReturnItems
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $capostReturnItems
     * @return OrderItem
     */
    public function addCapostReturnItems(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Item $capostReturnItems)
    {
        $this->capostReturnItems[] = $capostReturnItems;
        return $this;
    }

    /**
     * Get capostReturnItems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCapostReturnItems()
    {
        return $this->capostReturnItems;
    }
}
