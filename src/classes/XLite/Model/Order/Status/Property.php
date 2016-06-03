<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Order status config
 *
 * @Entity
 * @Table  (name="order_status_properties",
 *      indexes={
 *          @Index (name="payment_status", columns={"payment_status_id"}),
 *          @Index (name="shipping_status", columns={"shipping_status_id"})
 *      }
 * )
 */
class Property extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Payment status
     *
     * @var \XLite\Model\Order\Status\Payment
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order\Status\Payment", cascade={"all"})
     * @JoinColumn (name="payment_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $paymentStatus;

    /**
     * Shipping status
     *
     * @var \XLite\Model\Order\Status\Shipping
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order\Status\Shipping", cascade={"all"})
     * @JoinColumn (name="shipping_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $shippingStatus;

    /**
     * Increase (true) or decrease (false) inventory
     *
     * @var boolean 
     *
     * @Column (type="boolean", nullable=true)
     */
    protected $incStock;

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
     * Set incStock
     *
     * @param boolean $incStock
     * @return Property
     */
    public function setIncStock($incStock)
    {
        $this->incStock = $incStock;
        return $this;
    }

    /**
     * Get incStock
     *
     * @return boolean 
     */
    public function getIncStock()
    {
        return $this->incStock;
    }

    /**
     * Set paymentStatus
     *
     * @param \XLite\Model\Order\Status\Payment $paymentStatus
     * @return Property
     */
    public function setPaymentStatus(\XLite\Model\Order\Status\Payment $paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return \XLite\Model\Order\Status\Payment 
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set shippingStatus
     *
     * @param \XLite\Model\Order\Status\Shipping $shippingStatus
     * @return Property
     */
    public function setShippingStatus(\XLite\Model\Order\Status\Shipping $shippingStatus = null)
    {
        $this->shippingStatus = $shippingStatus;
        return $this;
    }

    /**
     * Get shippingStatus
     *
     * @return \XLite\Model\Order\Status\Shipping 
     */
    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }
}
