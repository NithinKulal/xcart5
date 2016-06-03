<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;

/**
 * Product
 *
 * @HasLifecycleCallbacks
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Whether pin codes are enabled for this product
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $pinCodesEnabled = false;

    /**
     * Whether to create pin codes automatically
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $autoPinCodes = false;

    /**
     * Pin codes (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\PINCodes\Model\PinCode", mappedBy="product")
     */
    protected $pinCodes;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->pinCodes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Returns true if product has pin codes enabled and pin code autogeneration is turned off
     *
     * @return boolean 
     */
    public function hasManualPinCodes()
    {
        return $this->getPinCodesEnabled() && !$this->getAutoPinCodes();
    }

    /**
     * Returns sold pins count 
     *
     * @return integer
     */
    public function getSoldPinCodesCount()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')->countSold($this);
    }

    /**
     * Returns remaining pins count 
     *
     * @return integer
     */
    public function getRemainingPinCodesCount()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode')->countRemaining($this);
    }

    /**
     * Sync amount in stock with remaining pin codes
     * 
     * @return void
     */
    public function syncAmount()
    {
        $remaining = $this->getRemainingPinCodesCount();

        if (parent::getAmount() !== $remaining) {
            $this->setAmount($remaining);
        }
    }

    /**
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();

        foreach ($this->getPinCodes() as $code) {
            if (!$code->getOrderItem()) {
                \XLite\Core\Database::getEM()->remove($code);
            }
        }
    }

    /**
     * Set pinCodesEnabled
     *
     * @param boolean $pinCodesEnabled
     * @return Product
     */
    public function setPinCodesEnabled($pinCodesEnabled)
    {
        $this->pinCodesEnabled = $pinCodesEnabled;
        return $this;
    }

    /**
     * Get pinCodesEnabled
     *
     * @return boolean 
     */
    public function getPinCodesEnabled()
    {
        return $this->pinCodesEnabled;
    }

    /**
     * Set autoPinCodes
     *
     * @param boolean $autoPinCodes
     * @return Product
     */
    public function setAutoPinCodes($autoPinCodes)
    {
        $this->autoPinCodes = $autoPinCodes;
        return $this;
    }

    /**
     * Get autoPinCodes
     *
     * @return boolean 
     */
    public function getAutoPinCodes()
    {
        return $this->autoPinCodes;
    }

    /**
     * Add pinCodes
     *
     * @param \XLite\Module\CDev\PINCodes\Model\PinCode $pinCodes
     * @return Product
     */
    public function addPinCodes(\XLite\Module\CDev\PINCodes\Model\PinCode $pinCodes)
    {
        $this->pinCodes[] = $pinCodes;
        return $this;
    }

    /**
     * Get pinCodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPinCodes()
    {
        return $this->pinCodes;
    }
}
