<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\ProductsReturn;

/**
 * Class represents a Canada Post return links
 *
 * @Entity
 * @Table  (name="capost_return_links")
 */
class Link extends \XLite\Module\XC\CanadaPost\Model\Base\Link
{
    /**
     * Reference to the Canada Post return model
     *
     * @var \XLite\Module\XC\CanadaPost\Model\ProductsReturn
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn", inversedBy="links")
     * @JoinColumn (name="returnId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $return;

    /**
     * Relation to a storage entity
     *
     * @var \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage
     *
     * @OneToOne (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage", mappedBy="link", cascade={"all"}, fetch="EAGER")
     */
    protected $storage;

	// {{{ Service methods

    /**
     * Set return
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Return model (OPTIONAL)
     *
     * @return void
     */
    public function setReturn(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return = null)
    {
        $this->return = $return;
    }

    /**
     * Set storage
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage $storage Storage object
     *
     * @return void
     */
    public function setStorage(\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage $storage)
    {
        $storage->setLink($this);
        
        $this->storage = $storage;
    }

	// }}}
    
    /**
     * Get store class
     *
     * @return string
     */
    protected function getStorageClass()
    {
        return '\XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage';
    }

    /**
     * Get filename for PDF documents
     *
     * @return string
     */
    public function getFileName()
    {
        $prefix = static::getAllowedRelsPrefixes($this->getRel());

        return $prefix . $this->getReturn()->getId() . $this->getReturn()->getOrder()->getOrderId() . '.pdf';
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
     * Set rel
     *
     * @param string $rel
     * @return Link
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
        return $this;
    }

    /**
     * Get rel
     *
     * @return string 
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Set href
     *
     * @param string $href
     * @return Link
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Get href
     *
     * @return string 
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set idx
     *
     * @param integer $idx
     * @return Link
     */
    public function setIdx($idx)
    {
        $this->idx = $idx;
        return $this;
    }

    /**
     * Get idx
     *
     * @return integer 
     */
    public function getIdx()
    {
        return $this->idx;
    }

    /**
     * Set mediaType
     *
     * @param string $mediaType
     * @return Link
     */
    public function setMediaType($mediaType)
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * Get mediaType
     *
     * @return string 
     */
    public function getMediaType()
    {
        return $this->mediaType;
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
     * Get storage
     *
     * @return \XLite\Module\XC\CanadaPost\Model\ProductsReturn\Link\Storage 
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
