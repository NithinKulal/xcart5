<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model;

/**
 * Product 
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product attachments
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"orderby" = "ASC"})
     */
    protected $attachments;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        foreach ($this->getAttachments() as $attachment) {
            $attachment->cloneEntityForProduct($newProduct);
        }
    
        $newProduct->update(true);

        return $newProduct;
    }

    /**
     * Add attachments
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachments
     * @return Product
     */
    public function addAttachments(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachments)
    {
        $this->attachments[] = $attachments;
        return $this;
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
