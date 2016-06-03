<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Abstract order status multilingual data
 *
 */
abstract class AStatusTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;

    /**
     * Customer name
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $customerName;

    /**
     * Return customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName ?: $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AStatusTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set customerName
     *
     * @param string $customerName
     * @return AStatusTranslation
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
        return $this;
    }

    /**
     * Get label_id
     *
     * @return integer 
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return AStatusTranslation
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
}
