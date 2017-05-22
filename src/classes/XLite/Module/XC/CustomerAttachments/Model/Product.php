<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Model;

/**
 * Decorate product model
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product is available for customer attachments
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isCustomerAttachmentsAvailable = false;

    /**
     * Attachment is required for add to cart
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isCustomerAttachmentsRequired = false;

    /**
     * Set isCustomerAttachmentsAvailable
     *
     * @param boolean $isCustomerAttachmentsAvailable
     * @return Product
     */
    public function setIsCustomerAttachmentsAvailable($isCustomerAttachmentsAvailable)
    {
        $this->isCustomerAttachmentsAvailable = $isCustomerAttachmentsAvailable;
        return $this;
    }

    /**
     * Get isCustomerAttachmentsAvailable
     *
     * @return boolean 
     */
    public function getIsCustomerAttachmentsAvailable()
    {
        return $this->isCustomerAttachmentsAvailable;
    }

    /**
     * Set isCustomerAttachmentsRequired
     *
     * @param boolean $isCustomerAttachmentsRequired
     * @return Product
     */
    public function setIsCustomerAttachmentsRequired($isCustomerAttachmentsRequired)
    {
        $this->isCustomerAttachmentsRequired = $isCustomerAttachmentsRequired;
        return $this;
    }

    /**
     * Get isCustomerAttachmentsRequired
     *
     * @return boolean 
     */
    public function getIsCustomerAttachmentsRequired()
    {
        return $this->isCustomerAttachmentsRequired;
    }

    /**
     * Check if attachment is mandatory for this product
     *
     * @return boolean
     */
    public function isCustomerAttachmentsMandatory()
    {
        return $this->getIsCustomerAttachmentsAvailable() && $this->getIsCustomerAttachmentsRequired();
    }
}