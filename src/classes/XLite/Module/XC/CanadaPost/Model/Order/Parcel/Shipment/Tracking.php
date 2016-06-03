<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment;

/**
 * Class represents a Canada Post parcel shipment tracking info (a return from "Get Tracking Details" request)
 *
 * @Entity
 * @Table  (name="order_capost_parcel_shipment_tracking")
 */
class Tracking extends \XLite\Model\AEntity
{
    /**
     * Maximum time to live
     */
    const MAX_TTL = 3600;

    /**
     * Shipment unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
	protected $id;

    /**
     * Tracking info shipment (reference to the Canada Post shipment model)
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment
     *
     * @OneToOne   (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment", inversedBy="trackingDetails")
     * @JoinColumn (name="shipmentId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $shipment;

    /**
     * This structure represents a list of delivery options
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\DeliveryOption", mappedBy="trackingDetails", cascade={"all"})
     */
    protected $deliveryOptions;

    /**
     * This structure represents a list of delivery events
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\SignificantEvent", mappedBy="trackingDetails", cascade={"all"})
     */
    protected $significantEvents;

    /**
     * This structure represents a list of tracking files
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File", mappedBy="trackingDetails", cascade={"all"})
     */
    protected $files;

    /**
     * Tracking expiration time (UNIX timestamp)
     *
     * @var integer
     *
     * @Column (type="integer", options={ "unsigned": true })
     */
    protected $expiry;
    
    /**
     * The PIN that can be used for other tracking calls
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $pin;

    /**
     * Indicates that the tracking information is contained in the active data repository
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $activeExists = false;

    /**
     * Indicates that the tracking information is contained in the archive data repository
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $archiveExists = false;

    /**
     * First 3 digits of the destination Postal Code for parcels to be delivered in Canada.
     * For international parcels this is the Postal Identifier of the destination country 
     * (e.g. ZIP code for parcels with U.S. destination) or an identifier of the destination country .
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $destinationPostalId;

    /**
     * The date the item is expected to reach the destination address for addresses in Canada.
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true, options={ "unsigned": true })
     */
    protected $expectedDeliveryDate;

    /**
     * Indicates a new expected delivery date
     *
     * @var integer
     *
     * @Column (type="integer", nullable=true, options={ "unsigned": true })
     */
    protected $changedExpectedDate;

    /**
     * Text description of reason for change to expected delivery date.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $changedExpectedDeliveryReason;

    /**
     * Customer number of the mailing customer.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $mailedByCustomerNumber;

    /**
     * Customer number of the mailed-on-behalf-of customer if applicable.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $mailedOnBehalfOfCustomerNumber;

    /**
     * In the situation where a parcel is being returned and the original parcel was created with an anticipated return, 
     * this field will contain the original PIN when the return PIN is queried.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $originalPin;

    /**
     * Canada Post service name in the language specified by the request. (The service-name will default to English if language is not specified).
     * For inbound international parcels, service type is empty or contains the service name from its shipper of origin if available.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $serviceName;

    /**
     * Canada Post service name in the other Canadian official language.
     * For inbound international parcels, service name is empty or contains the service name from its shipper of origin.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $serviceName_2;

    /**
     * The value supplied by the shipper as customer-ref-1 when the shipment was first created with Canada Post.
     * French character set is supported through this interface.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $customerRef_1;

    /**
     * The value supplied by the shipper as customer reference 2 when the shipment was first created with Canada Post.
     * Special characters should be avoided when entering customer reference numbers.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $customerRef_2;

    /**
     * In the situation where a parcel is being sent with an anticipated return, this field will contain the return PIN when the original PIN is queried.
     *
     * @var string
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $returnPin;

    /**
     * True indicates that Get Signature Image will return a signature image. If false, there is no need to call "Get Signature Image" because it will return “not found”.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $signatureImageExists = false;

    /**
     * Indicates whether a signature image collected by Canada Post for domestic parcels has been requested to be suppressed for viewing by the recipient of a parcel.
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $suppressSignature = false;

	// {{{ Service methods

    /**
     * Constructor
     *
     * @param array $data Entity properties (OPTIONAL)
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->deliveryOptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->significantEvents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add an delivery option
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\DeliveryOption $newDeliveryOption Delivery option object 
     *
     * @return void
     */
    public function addDeliveryOption(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\DeliveryOption $newDeliveryOption)
    {
        $newDeliveryOption->setTrackingDetails($this);

        $this->addDeliveryOptions($newDeliveryOption);
    }

    /**
     * Add an significant event
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\SignificantEvent $newSignificantEvent Significant event object 
     *
     * @return void
     */
    public function addSignificantEvent(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\SignificantEvent $newSignificantEvent)
    {
        $newSignificantEvent->setTrackingDetails($this);

        $this->addSignificantEvents($newSignificantEvent);
    }

    /**
     * Set shipment
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipment Shipment object (OPTIONAL)
     *
     * @return void
     */
    public function setShipment(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment $shipment = null)
    {
        $this->shipment = $shipment;
    }

    /**
     * Add a file
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $newFile Tracking file
     *
     * @return void
     */
    public function addFile(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $newFile)
    {
        $newFile->setTrackingDetails($this);

        $this->addFiles($newFile);
    }

	// }}}

    // {{{ Download tracking files methods
    
    /**
     * Download files related to the tracking details
     *
     * @param boolean $flush Flag - flush changes or not (OPTIONAL)
     *
     * @return void
     */
    public function downloadFiles($flush = true)
    {
        // Download Signature Image first
        if ($this->getSignatureImageExists()) {
            $this->downloadSignatureImage(false);
        }

        // Download delivery confirmation certificate 
        $this->downloadDeliveryConfirmationCertificate(false);
        
        if ($flush) {
            \XLite\Core\Database::getEM()->flush();
        }
    }
    
    /**
     * Download signature image
     *
     * @param boolean $flush Flag - flush changes or not (OPTIONAL)
     *
     * @return boolean
     */
    protected function downloadSignatureImage($flush = true)
    {
        $result = false;

        $data = \XLite\Module\XC\CanadaPost\Core\Service\Tracking::getInstance()
            ->callGetSignatureImageByPinNumber($this->getRealTrackingPin());

        if (isset($data->signatureImage)) {

            $this->saveTrackingFile(
                $data->signatureImage,
                \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File::DOCTYPE_SIGN_IMAGE,
                $flush
            );

            $result = true;
        }

        return $result;
    }

    /**
     * Download delivery confirmation certificate
     *
     * @param boolean $flush  Flag - flush changes or not (OPTIONAL)
     *
     * @return boolean
     */
    protected function downloadDeliveryConfirmationCertificate($flush = true)
    {
        $result = false;

        $data = \XLite\Module\XC\CanadaPost\Core\Service\Tracking::getInstance()
            ->callGetDeliveryConfirmCertByPinNumber($this->getRealTrackingPin());

        if (isset($data->deliveryConfirmationCertificate)) {

            $this->saveTrackingFile(
                $data->deliveryConfirmationCertificate,
                \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File::DOCTYPE_DCONG_CERT,
                $flush
            );

            $result = true;
        }

        return $result;
    }

    /**
     * Save tracking file
     *
     * @param \XLite\Core\CommonCell $data    File data
     * @param string                 $docType Document type
     * @param boolean                $flush   Flag - flush changes or not (OPTIONAL)
     *
     * @return void
     */
    protected function saveTrackingFile($data, $docType, $flush = true)
    {
        // Save file to temporary location
        $filePath = LC_DIR_TMP . 't' . strtolower($docType) . $this->getShipment()->getId() . '_' . $data->filename;

        \Includes\Utils\FileManager::write($filePath, base64_decode($data->image));

        $file = $this->getFileByDocType($docType);

        if (!isset($file)) {

            $file = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File();

            $file->setDocType($docType);

            \XLite\Core\Database::getEM()->persist($file);

            $this->addFile($file);
        }

        $file->loadFromLocalFile($filePath);
        $file->setMime($data->mimeType);

        if ($flush) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    // }}}
    
    /** 
     * Check - is tracking details have files
     *
     * @return boolean
     */
    public function hasFiles()
    {
        return 0 < $this->getFiles()->count();
    }

    /**
     * Get non-masket tracking pin
     *
     * @return string
     */
    public function getRealTrackingPin()
    {
        return $this->getShipment()->getTrackingPin();
    }

    /**
     * Update expiration time
     *
     * @return void
     */
    public function updateExpiry()
    {
        $this->setExpiry(\XLite\Core\Converter::time() + self::MAX_TTL);
    }
    
    /**
     * Check - is tracking data are expired or not
     *
     * @return boolean
     */
    public function isExpired()
    {
        return (\XLite\Core\Converter::time() > $this->getExpiry());
    }
    
    /**
     * Get signature image
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File|null
     */
    public function getSignatureImage()
    {
        return $this->getFileByDocType(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File::DOCTYPE_SIGN_IMAGE);
    }
    
    /**
     * Get delivery confirmation certificate
     * 
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File|null
     */
    public function getDeliveryConfirmationCertificate()
    {
        return $this->getFileByDocType(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File::DOCTYPE_DCONG_CERT);
    }
    
    /**
     * Get tracking file by it's type
     *
     * @param string $docType Document type
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File|null
     */
    public function getFileByDocType($docType)
    {
        $file = null;

        foreach ($this->getFiles() as $f) {

            if ($docType == $f->getDocType()) {
                $file = $f;
                break;
            }
        }

        return $file;
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
     * Set expiry
     *
     * @param integer $expiry
     * @return Tracking
     */
    public function setExpiry($expiry)
    {
        $this->expiry = $expiry;
        return $this;
    }

    /**
     * Get expiry
     *
     * @return integer 
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set pin
     *
     * @param string $pin
     * @return Tracking
     */
    public function setPin($pin)
    {
        $this->pin = $pin;
        return $this;
    }

    /**
     * Get pin
     *
     * @return string 
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set activeExists
     *
     * @param boolean $activeExists
     * @return Tracking
     */
    public function setActiveExists($activeExists)
    {
        $this->activeExists = $activeExists;
        return $this;
    }

    /**
     * Get activeExists
     *
     * @return boolean 
     */
    public function getActiveExists()
    {
        return $this->activeExists;
    }

    /**
     * Set archiveExists
     *
     * @param boolean $archiveExists
     * @return Tracking
     */
    public function setArchiveExists($archiveExists)
    {
        $this->archiveExists = $archiveExists;
        return $this;
    }

    /**
     * Get archiveExists
     *
     * @return boolean 
     */
    public function getArchiveExists()
    {
        return $this->archiveExists;
    }

    /**
     * Set destinationPostalId
     *
     * @param string $destinationPostalId
     * @return Tracking
     */
    public function setDestinationPostalId($destinationPostalId)
    {
        $this->destinationPostalId = $destinationPostalId;
        return $this;
    }

    /**
     * Get destinationPostalId
     *
     * @return string 
     */
    public function getDestinationPostalId()
    {
        return $this->destinationPostalId;
    }

    /**
     * Set expectedDeliveryDate
     *
     * @param integer $expectedDeliveryDate
     * @return Tracking
     */
    public function setExpectedDeliveryDate($expectedDeliveryDate)
    {
        $this->expectedDeliveryDate = $expectedDeliveryDate;
        return $this;
    }

    /**
     * Get expectedDeliveryDate
     *
     * @return integer 
     */
    public function getExpectedDeliveryDate()
    {
        return $this->expectedDeliveryDate;
    }

    /**
     * Set changedExpectedDate
     *
     * @param integer $changedExpectedDate
     * @return Tracking
     */
    public function setChangedExpectedDate($changedExpectedDate)
    {
        $this->changedExpectedDate = $changedExpectedDate;
        return $this;
    }

    /**
     * Get changedExpectedDate
     *
     * @return integer 
     */
    public function getChangedExpectedDate()
    {
        return $this->changedExpectedDate;
    }

    /**
     * Set changedExpectedDeliveryReason
     *
     * @param string $changedExpectedDeliveryReason
     * @return Tracking
     */
    public function setChangedExpectedDeliveryReason($changedExpectedDeliveryReason)
    {
        $this->changedExpectedDeliveryReason = $changedExpectedDeliveryReason;
        return $this;
    }

    /**
     * Get changedExpectedDeliveryReason
     *
     * @return string 
     */
    public function getChangedExpectedDeliveryReason()
    {
        return $this->changedExpectedDeliveryReason;
    }

    /**
     * Set mailedByCustomerNumber
     *
     * @param string $mailedByCustomerNumber
     * @return Tracking
     */
    public function setMailedByCustomerNumber($mailedByCustomerNumber)
    {
        $this->mailedByCustomerNumber = $mailedByCustomerNumber;
        return $this;
    }

    /**
     * Get mailedByCustomerNumber
     *
     * @return string 
     */
    public function getMailedByCustomerNumber()
    {
        return $this->mailedByCustomerNumber;
    }

    /**
     * Set mailedOnBehalfOfCustomerNumber
     *
     * @param string $mailedOnBehalfOfCustomerNumber
     * @return Tracking
     */
    public function setMailedOnBehalfOfCustomerNumber($mailedOnBehalfOfCustomerNumber)
    {
        $this->mailedOnBehalfOfCustomerNumber = $mailedOnBehalfOfCustomerNumber;
        return $this;
    }

    /**
     * Get mailedOnBehalfOfCustomerNumber
     *
     * @return string 
     */
    public function getMailedOnBehalfOfCustomerNumber()
    {
        return $this->mailedOnBehalfOfCustomerNumber;
    }

    /**
     * Set originalPin
     *
     * @param string $originalPin
     * @return Tracking
     */
    public function setOriginalPin($originalPin)
    {
        $this->originalPin = $originalPin;
        return $this;
    }

    /**
     * Get originalPin
     *
     * @return string 
     */
    public function getOriginalPin()
    {
        return $this->originalPin;
    }

    /**
     * Set serviceName
     *
     * @param string $serviceName
     * @return Tracking
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string 
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set serviceName_2
     *
     * @param string $serviceName2
     * @return Tracking
     */
    public function setServiceName2($serviceName2)
    {
        $this->serviceName_2 = $serviceName2;
        return $this;
    }

    /**
     * Get serviceName_2
     *
     * @return string 
     */
    public function getServiceName2()
    {
        return $this->serviceName_2;
    }

    /**
     * Set customerRef_1
     *
     * @param string $customerRef1
     * @return Tracking
     */
    public function setCustomerRef1($customerRef1)
    {
        $this->customerRef_1 = $customerRef1;
        return $this;
    }

    /**
     * Get customerRef_1
     *
     * @return string 
     */
    public function getCustomerRef1()
    {
        return $this->customerRef_1;
    }

    /**
     * Set customerRef_2
     *
     * @param string $customerRef2
     * @return Tracking
     */
    public function setCustomerRef2($customerRef2)
    {
        $this->customerRef_2 = $customerRef2;
        return $this;
    }

    /**
     * Get customerRef_2
     *
     * @return string 
     */
    public function getCustomerRef2()
    {
        return $this->customerRef_2;
    }

    /**
     * Set returnPin
     *
     * @param string $returnPin
     * @return Tracking
     */
    public function setReturnPin($returnPin)
    {
        $this->returnPin = $returnPin;
        return $this;
    }

    /**
     * Get returnPin
     *
     * @return string 
     */
    public function getReturnPin()
    {
        return $this->returnPin;
    }

    /**
     * Set signatureImageExists
     *
     * @param boolean $signatureImageExists
     * @return Tracking
     */
    public function setSignatureImageExists($signatureImageExists)
    {
        $this->signatureImageExists = $signatureImageExists;
        return $this;
    }

    /**
     * Get signatureImageExists
     *
     * @return boolean 
     */
    public function getSignatureImageExists()
    {
        return $this->signatureImageExists;
    }

    /**
     * Set suppressSignature
     *
     * @param boolean $suppressSignature
     * @return Tracking
     */
    public function setSuppressSignature($suppressSignature)
    {
        $this->suppressSignature = $suppressSignature;
        return $this;
    }

    /**
     * Get suppressSignature
     *
     * @return boolean 
     */
    public function getSuppressSignature()
    {
        return $this->suppressSignature;
    }

    /**
     * Get shipment
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment 
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Add deliveryOptions
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\DeliveryOption $deliveryOptions
     * @return Tracking
     */
    public function addDeliveryOptions(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\DeliveryOption $deliveryOptions)
    {
        $this->deliveryOptions[] = $deliveryOptions;
        return $this;
    }

    /**
     * Get deliveryOptions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDeliveryOptions()
    {
        return $this->deliveryOptions;
    }

    /**
     * Add significantEvents
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\SignificantEvent $significantEvents
     * @return Tracking
     */
    public function addSignificantEvents(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\SignificantEvent $significantEvents)
    {
        $this->significantEvents[] = $significantEvents;
        return $this;
    }

    /**
     * Get significantEvents
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSignificantEvents()
    {
        return $this->significantEvents;
    }

    /**
     * Add files
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $files
     * @return Tracking
     */
    public function addFiles(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $files)
    {
        $this->files[] = $files;
        return $this;
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}
