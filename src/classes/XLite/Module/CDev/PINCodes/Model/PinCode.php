<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;

/**
 * PIN Code 
 *
 * @Entity
 * @Table  (
 *      name="pin_codes",
 *      uniqueConstraints={
 *          @UniqueConstraint (name="productCode", columns={"code", "productId"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class PinCode extends \XLite\Model\AEntity
{
    /**
     * PIN Code unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Code
     *
     * @var string
     *
     * @Column (type="string", length=64)
     */
    protected $code = '';

    /**
     * Create date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $createDate;

    /**
     * Is sold
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isSold = false;

    /**
     * Is blocked
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isBlocked = false;

    /**
     * Product (relation)
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="pinCodes")
     * @JoinColumn (name="productId", referencedColumnName="product_id", onDelete="SET NULL")
     */
    protected $product;

    /**
     * OrderItem (relation)
     *
     * @var \XLite\Model\OrderItem
     *
     * @ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="pinCodes")
     * @JoinColumn (name="orderItemId", referencedColumnName="item_id", onDelete="SET NULL")
     */
    protected $orderItem;

    
    /**
     * Generate pin code 
     *
     * @return string
     * 
     * @throws \Exception on attempt to autogenerate without $product defined
     */
    public function generateCode()
    {
        if (!$this->getProduct()) {
            throw new \Exception('Can not ensure pin uniqueness without a product assigned to this pin code');
        }
    
        $newValue = null;
        $repo = \XLite\Core\Database::getRepo('XLite\Module\CDev\PINCodes\Model\PinCode');
        while (!$newValue || $repo->findOneBy(array('code' => $newValue, 'product' => $this->getProduct()->getId()))) {
            $newValue = $this->getRandomCode();
        }
        $this->code = $newValue;

        return $this->code;
    }

    /**
     * Prepare creation date 
     * 
     * @return void
     *
     * @PrePersist
     */
    public function prepareDate()
    {
        if (!$this->getCreateDate()) {
            $this->setCreateDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Generates random pin code 
     * 
     * @return string
     */
    protected function getRandomCode()
    {
        return sprintf('%04d%04d%04d%04d', rand(0, 9999), rand(0, 9999), rand(0, 9999), rand(0, 9999));
    }


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
     * Set code
     *
     * @param string $code
     * @return PinCode
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set createDate
     *
     * @param integer $createDate
     * @return PinCode
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * Get createDate
     *
     * @return integer 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set isSold
     *
     * @param boolean $isSold
     * @return PinCode
     */
    public function setIsSold($isSold)
    {
        $this->isSold = $isSold;
        return $this;
    }

    /**
     * Get isSold
     *
     * @return boolean 
     */
    public function getIsSold()
    {
        return $this->isSold;
    }

    /**
     * Set isBlocked
     *
     * @param boolean $isBlocked
     * @return PinCode
     */
    public function setIsBlocked($isBlocked)
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    /**
     * Get isBlocked
     *
     * @return boolean 
     */
    public function getIsBlocked()
    {
        return $this->isBlocked;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return PinCode
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set orderItem
     *
     * @param \XLite\Model\OrderItem $orderItem
     * @return PinCode
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
        return $this;
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
