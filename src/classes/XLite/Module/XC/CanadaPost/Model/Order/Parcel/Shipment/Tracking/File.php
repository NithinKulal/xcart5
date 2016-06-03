<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking;

/**
 * Class represents a Canada Post tracking files 
 *
 * @Entity
 * @Table  (name="order_capost_parcel_shipment_tracking_files")
 */
class File extends \XLite\Model\Base\Storage
{
    /**
     * Document type
     */
    const DOCTYPE_SIGN_IMAGE = 'I';
    const DOCTYPE_DCONG_CERT = 'C';

    /**
     * Tracking document type
     *
     * @param string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $docType = self::DOCTYPE_SIGN_IMAGE;

    /**
     * Relation to a Canada Post tracking entity
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking", inversedBy="files")
     * @JoinColumn (name="trackingId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $trackingDetails;

    // {{{ Service methods

    /**
     * Set tracking details
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking $tracking Tracking object (OPTIONAL)
     *
     * @return void
     */
    public function setTrackingDetails(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking $tracking = null)
    {
        $this->trackingDetails = $tracking;
    }

    /**
     * Get Order ID
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->getTrackingDetails()->getShipment()->getParcel()->getOrder()->getOrderId();
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
        return $this->getOrderId() . LC_DS . parent::assembleSavePath($path);
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        $path = parent::getStoreFileSystemRoot() . $this->getOrderId() . LC_DS;

        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }
    
    // }}}
    
    /**
     * Get allowed document types
     *
     * @param string $type Document type to get (OPTIONAL)
     *
     * @return array
     */
    public static function getAllowedDoctypes($type = null)
    {
        $list = array(
            static::DOCTYPE_SIGN_IMAGE => 'Signature image',
            static::DOCTYPE_DCONG_CERT => 'Delivery confirmation certificate',
        );

        return (isset($type)) ? ((isset($list[$type])) ? $list[$type] : null) : $list;
    }

    /**
     * Get file title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::getAllowedDoctypes($this->getDocType());
    }

    /**
     * Set docType
     *
     * @param string $docType
     * @return File
     */
    public function setDocType($docType)
    {
        $this->docType = $docType;
        return $this;
    }

    /**
     * Get docType
     *
     * @return string 
     */
    public function getDocType()
    {
        return $this->docType;
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
     * Set path
     *
     * @param string $path
     * @return File
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
     * @return File
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
     * @return File
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
     * @return File
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
     * @return File
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
     * @return File
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
     * Get trackingDetails
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking 
     */
    public function getTrackingDetails()
    {
        return $this->trackingDetails;
    }
}
