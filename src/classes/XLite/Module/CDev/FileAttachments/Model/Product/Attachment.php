<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model\Product;

/**
 * Product attchament 
 *
 * @Entity
 * @Table  (name="product_attachments",
 *      indexes={
 *          @Index (name="o", columns={"orderby"})
 *      }
 * )
 */
class Attachment extends \XLite\Model\Base\I18n
{
    // {{{ Collumns

    /**
     * Unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Sort position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $orderby = 0;

    // }}}

    // {{{ Associations

    /**
     * Relation to a product entity
     *
     * @var \XLite\Model\Product
     *
     * @ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="attachments")
     * @JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Relation to a product entity
     *
     * @var \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage
     *
     * @OneToOne  (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage", mappedBy="attachment", cascade={"all"}, fetch="EAGER")
     */
    protected $storage;

    // }}}

    // {{{ Getters / setters

    /**
     * Get storage 
     * 
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage
     */
    public function getStorage()
    {
        if (!$this->storage) {
            $this->setStorage(new \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage);
            $this->storage->setAttachment($this);
        }

        return $this->storage;
    }

    /**
     * Get public title 
     * 
     * @return string
     */
    public function getPublicTitle()
    {
        return $this->getTitle() ?: $this->getStorage()->getFileName();
    }

    // }}}

    /**
     * Clone for product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntityForProduct(\XLite\Model\Product $product)
    {
        $newAttachment = parent::cloneEntity();
        
        $newAttachment->setProduct($product);
        $product->addAttachments($newAttachment);

        $this->getStorage()->cloneEntityForAttachment($newAttachment);

        return $newAttachment;
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
     * Set orderby
     *
     * @param integer $orderby
     * @return Attachment
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer 
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return Attachment
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
     * Set storage
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage $storage
     * @return Attachment
     */
    public function setStorage(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage $storage = null)
    {
        $this->storage = $storage;
        return $this;
    }
}
