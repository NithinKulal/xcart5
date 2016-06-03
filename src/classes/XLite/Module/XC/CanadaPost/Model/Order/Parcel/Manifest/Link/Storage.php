<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link;

/**
 * Link's storage 
 *
 * @Entity
 * @Table  (name="order_capost_parcel_manifest_link_storage")
 */
class Storage extends \XLite\Model\Base\Storage
{
    // {{{ Associations

    /**
     * Relation to a link
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link
     *
     * @OneToOne   (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link", inversedBy="storage")
     * @JoinColumn (name="linkId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $link;

    // }}}

    // {{{ Service operations

    /**
     * Set link
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $link Link object (OPTIONAL)
     *
     * @return void
     */
    public function setLink(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link $link = null)
    {
        $this->link = $link;
    }

    /**
     * Assemble path for save into DB
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function assembleSavePath($path)
    {
        $shipments = $this->getLink()->getManifest()->getShipments();

        return $shipments[0]->getParcel()->getOrder()->getOrderId() . LC_DS . parent::assembleSavePath($path);
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        $shipments = $this->getLink()->getManifest()->getShipments();

        $path = parent::getStoreFileSystemRoot() . $shipments[0]->getParcel()->getOrder()->getOrderId() . LC_DS;

        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
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
     * Set path
     *
     * @param string $path
     * @return Storage
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Storage
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mime
     *
     * @param string $mime
     * @return Storage
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * Get mime
     *
     * @return string 
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set storageType
     *
     * @param string $storageType
     * @return Storage
     */
    public function setStorageType($storageType)
    {
        $this->storageType = $storageType;
        return $this;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Storage
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Storage
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get link
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link 
     */
    public function getLink()
    {
        return $this->link;
    }
}
