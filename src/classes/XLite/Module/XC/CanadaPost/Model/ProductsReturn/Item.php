<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\ProductsReturn;

/**
 * Class represents an return items model
 *
 * @Entity
 * @Table  (name="capost_return_items")
 */
class Item extends \XLite\Model\AEntity
{
    /**
     * Item unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Reference to the return model
     *
     * @var \XLite\Module\XC\CanadaPost\Model\ProductsReturn
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn", inversedBy="items")
     * @JoinColumn (name="returnId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $return;

    /**
     * Reference to the order item model
     *
     * @var \XLite\Model\OrderItem 
     *
     * @ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="capostReturnItems")
     * @JoinColumn (name="orderItemId", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $orderItem;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = 0;

    // {{{ Service methods

    /**
     * Assign the return
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Products return (OPTIONAL)
     *
     * @return void
     */
    public function setReturn(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return = null)
    {
        $this->return = $return;
    }

    /**
     * Assign the order item
     *
     * @param \XLite\Model\OrderItem $orderItem Order's item (OPTIONAL)
     *
     * @return void
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
    }

    // }}}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return Item
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get return
     *
     * @return \XLite\Module\XC\CanadaPost\Model\ProductsReturn 
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * Get orderItem
     *
     * @return \XLite\Model\OrderItem 
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }
}
