<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking;

/**
 * Class represents a Canada Post tracking delivery options
 *
 * @Entity
 * @Table  (name="order_capost_parcel_shipment_tracking_options")
 */
class DeliveryOption extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Shipment tracking details (reference to the Canada Post tracking model)
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking", inversedBy="deliveryOptions")
     * @JoinColumn (name="trackingId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $trackingDetails;

    /**
     * The string representing the delivery-option
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $name;
    
    /**
     * The description of the delivery option
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $description;


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
     * Set name
     *
     * @param string $name
     * @return DeliveryOption
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
     * Set description
     *
     * @param string $description
     * @return DeliveryOption
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
