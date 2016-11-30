<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Order;

/**
 * Class represents a Canada Post parcel
 *
 * @Entity
 * @Table (name="order_capost_parcels",
 *      indexes={
 *          @Index (name="status", columns={"status"}),
 *          @Index (name="number", columns={"number"})
 *      }
 * )
 */
class Parcel extends \XLite\Model\AEntity
{
    /**
     * Parcel statuses
     */
    const STATUS_PROPOSED   = 'P';
    const STATUS_CREATED    = 'C';
    const STATUS_TRANSMITED = 'T';

    /**
     * Quote types
     */
    const QUOTE_TYPE_CONTRACTED     = 'C';
    const QUOTE_TYPE_NON_CONTRACTED = 'N';

    /**
     * Parcel (shipment) options
     */
    const OPT_DEFAULT              = '';
    // Delivery way types
    const OPT_WTD_HOLD_FOR_PICK_UP = 'HFP';
    const OPT_WTD_LEAVE_AT_DOOR    = 'LAD';
    const OPT_WTD_DO_NOT_SAFE_DROP = 'DNS';
    // Age proof values
    const OPT_AGE_PROOF_18         = 'PA18';
    const OPT_AGE_PROOF_19         = 'PA19';
    // Non-delivery handling codes (required for some U.S.A. and international shipments)
    const OPT_RET_AT_SENDER_EXP    = 'RASE';
    const OPT_RET_TO_SENDER        = 'RTS';
    const OPT_ABANDON              = 'ABAN';
    // Other parcel (shipment) options codes
    const OPT_SIGNATURE            = 'SO';
    const OPT_COVERAGE             = 'COV';
    const OPT_COD                  = 'COD';
    const OPT_DELIVER_TO_PO        = 'D2PO';

    /**
     * Options classes
     */
    const OPT_CLASS_WAY_TO_DELIVER = 'way_to_deliver';
    const OPT_CLASS_AGE_PROOF      = 'age_proof';
    const OPT_CLASS_SIGNATURE      = 'signature';
    const OPT_CLASS_COVERAGE       = 'coverage';
    const OPT_CLASS_NON_DELIVERY   = 'non_delivery';

    /**
     * Options schema fields
     */
    const OPT_SCHEMA_CLASS           = 'class';
    const OPT_SCHEMA_TITLE           = 'title';
    const OPT_SCHEMA_TEMPLATE        = 'template';
    const OPT_SCHEMA_OPTIONS         = 'options';
    const OPT_SCHEMA_ALLOWED_OPTIONS = 'allowedOptions';
    const OPT_SCHEMA_MANDATORY       = 'mandatory';
    const OPT_SCHEMA_MULTIPLE        = 'multiple';

    /**
     * Parcel unique id
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $id;

    /**
     * Parcel number
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $number;

    /**
     * Status code
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $status = self::STATUS_PROPOSED;

    /**
     * Previous status code
     *
     * @var string
     */
    protected $oldStatus = self::STATUS_PROPOSED;

    /**
     * Quote type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $quoteType = self::QUOTE_TYPE_NON_CONTRACTED;

    /**
     * Parcel's order (referece to the orders model)
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="capostParcels")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Parcel items (referece to the parcel's items model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item", mappedBy="parcel", cascade={"all"})
     */
    protected $items;

    /**
     * Parcel shipment info (return from "Create Shipment" and "Create Non-Contract Shipment" requests)
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment
     *
     * @OneToOne (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment", mappedBy="parcel", cascade={"all"})
     */
    protected $shipment;

    // {{{ Parcel dimensions and weight

    /**
     * Parcel box weight (max weight)
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxWeight = 0.0000;

    /**
     * Parcel box width
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxWidth = 0.0000;

    /**
     * Parcel box length
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxLength = 0.0000;

    /**
     * Parcel box height
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $boxHeight = 0.0000;

    // }}}

    // {{{ Parcel types

    /**
     * Is parcel a document
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isDocument = false;

    /**
     * Is parcel unpackaged
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isUnpackaged = false;

    /**
     * Is parcel a mailing tube
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isMailingTube = false;

    /**
     * Is parcel oversized
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $isOversized = false;

    // }}}

    // {{{ Notifications

    /**
     * Send notification on shipment
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $notifyOnShipment = false;

    /**
     * Send notification on exception
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $notifyOnException = false;

    /**
     * Send notification on delivery
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $notifyOnDelivery = false;

    // }}}

    // {{{ Parcel options

    /**
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $optSignature = false;

    /**
     * Option "Coverage amount"
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $optCoverage = 0.0000;

    /**
     * Options "Proof of age required - 18" and "Proof of age required - 19"
     *
     * @var string
     *
     * @Column (type="string", length=4)
     */
    protected $optAgeProof = self::OPT_DEFAULT;

    /**
     * Delivery way type (the "Card for pickup", "Do not safe drop", "Leave at door" options)
     *
     * @var string
     *
     * @Column (type="string", length=3)
     */
    protected $optWayToDeliver = self::OPT_DEFAULT;

    /**
     * Non-delivery handling type (the "Return at Sender’s Expense", "Return to Sender", "Abandon" options)
     *
     * @var string
     *
     * @Column (type="string", length=4)
     */
    protected $optNonDelivery = self::OPT_DEFAULT;

    // }}}

    /**
     * Canada Post API calls errors
     *
     * @var null|array
     */
    protected $apiCallErrors = null;

    /**
     * Parcel status handlers list
     *
     * @var array
     */
    protected static $statusHandlers = array(

        self::STATUS_PROPOSED => array(
            self::STATUS_CREATED     => 'create',
        ),

        self::STATUS_CREATED => array(
            self::STATUS_PROPOSED    => 'propose',
            self::STATUS_TRANSMITED  => 'transmit',
        ),

        self::STATUS_TRANSMITED      => array(),
    );

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
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Set old status (not stored in the DB)
     *
     * @param string $value Status code
     *
     * @return void
     */
    public function setOldStatus($value)
    {
        $this->oldStatus = $value;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order object (OPTIONAL)
     *
     * @return void
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Add an item to parcel
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $newItem Item object
     *
     * @return void
     */
    public function addItem(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $newItem)
    {
        $newItem->setParcel($this);

        $this->addItems($newItem);
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
        if (null !== $shipment) {
            $shipment->setParcel($this);
        }

        $this->shipment = $shipment;
    }

    /**
     * Clone object
     *
     * @param boolean $cloneItems Clone parcel items
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity($cloneItems = true)
    {
        $newParcel = parent::cloneEntity();

        if ($this->getOrder()) {
            $newParcel->setOrder($this->getOrder());
        }

        if ($cloneItems
            && $this->hasItems()
        ) {
            foreach ($this->getItems() as $item) {
                $newParcel->addItem($item);
            }
        }

        return $newParcel;
    }

    // }}}

    // {{{ Parcel (shipment) options methods

    /**
     * Get list of all valid parcel (shipment) options codes and their names
     *
     * @param string|null $option Option code
     *
     * @return array|null
     */
    public static function getValidOptions($option = null)
    {
        $list = array(

            // "Way to deliver" class options
            static::OPT_WTD_HOLD_FOR_PICK_UP => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Card (hold) for pickup',
            ),
            static::OPT_WTD_LEAVE_AT_DOOR    => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Leave at door - do not card',
            ),
            static::OPT_WTD_DO_NOT_SAFE_DROP => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_WAY_TO_DELIVER,
                static::OPT_SCHEMA_TITLE     => 'Do not safe drop',
            ),

            // "Proof of age" class options
            static::OPT_AGE_PROOF_18         => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_AGE_PROOF,
                static::OPT_SCHEMA_TITLE     => 'Proof of Age Required - 18',
            ),
            static::OPT_AGE_PROOF_19         => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_AGE_PROOF,
                static::OPT_SCHEMA_TITLE     => 'Proof of Age Required - 19',
            ),

            // "Signature" class options
            static::OPT_SIGNATURE            => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_SIGNATURE,
                static::OPT_SCHEMA_TITLE     => 'Signature',
            ),

            // "Coverage" class options
            static::OPT_COVERAGE             => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_COVERAGE,
                static::OPT_SCHEMA_TITLE     => 'Coverage',
            ),

            // "Collect on delivery" option (not implemented yet)
            static::OPT_COD                  => array(
                static::OPT_SCHEMA_TITLE     => 'Collect on delivery',
            ),

            // "Deliver to Post Office" option (not implemented yet)
            static::OPT_DELIVER_TO_PO        => array(
                static::OPT_SCHEMA_TITLE     => 'Deliver to Post Office',
            ),

            // "Non-delivery handling" class options
            static::OPT_RET_AT_SENDER_EXP    => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Return at Sender\'s Expense',
            ),
            static::OPT_RET_TO_SENDER        => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Return to Sender',
            ),
            static::OPT_ABANDON              => array(
                static::OPT_SCHEMA_CLASS     => static::OPT_CLASS_NON_DELIVERY,
                static::OPT_SCHEMA_TITLE     => 'Abandon',
            ),
        );

        return null !== $option
            ? (isset($list[$option]) ? $list[$option] : null)
            : $list;
    }

    /**
     * Get list of specified class valid parcel (shipment) options codes and their names
     *
     * @param string $optionsClass Options class
     *
     * @return array|null
     */
    public static function getValidOptionsByClass($optionsClass)
    {
        $classOptions = array();

        if (!empty($optionsClass)) {
            $options = static::getValidOptions();

            foreach ($options as $k => $v) {
                if (isset($v[static::OPT_SCHEMA_CLASS]) && $optionsClass == $v[static::OPT_SCHEMA_CLASS]) {
                    $classOptions[$k] = $v;
                }
            }
        }

        return (empty($classOptions)) ? null : $classOptions;
    }

    /**
     * Get all visible valid parcel options classes
     *
     * @return array
     */
    public static function getValidOptionClasses()
    {
        $list = array(
            static::OPT_CLASS_WAY_TO_DELIVER => array(
                static::OPT_SCHEMA_TITLE     => 'Way to deliver',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/way_to_deliver.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ),
            static::OPT_CLASS_SIGNATURE      => array(
                static::OPT_SCHEMA_TITLE     => 'Signature',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/signature.twig',
                static::OPT_SCHEMA_MULTIPLE  => false,
            ),
            static::OPT_CLASS_AGE_PROOF      => array(
                static::OPT_SCHEMA_TITLE     => 'Proof of age',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/age_proof.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ),
            static::OPT_CLASS_COVERAGE       => array(
                static::OPT_SCHEMA_TITLE     => 'Coverage amount',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/coverage.twig',
                static::OPT_SCHEMA_MULTIPLE  => false,
            ),
            static::OPT_CLASS_NON_DELIVERY   => array(
                static::OPT_SCHEMA_TITLE     => 'Non-delivery instructions',
                static::OPT_SCHEMA_TEMPLATE  => 'modules/XC/CanadaPost/shipments/options/non_delivery.twig',
                static::OPT_SCHEMA_MULTIPLE  => true,
            ),
        );

        return $list;
    }

    /**
     * Get all allowed options classes for the parcel
     *
     * @return array
     */
    public function getAllowedOptionClasses()
    {
        $service = $this->getOrder()->getCapostDeliveryService();

        $allClasses = static::getValidOptionClasses();

        $allowedClasses = array();

        if (null !== $service) {
            $allOptions = static::getValidOptions();

            foreach ($service->getOptions() as $k => $v) {
                $code = $v->getCode();

                if (isset($allOptions[$code])
                    && isset($allOptions[$code][static::OPT_SCHEMA_CLASS])
                ) {
                    $class = $allOptions[$code][static::OPT_SCHEMA_CLASS];

                    if (!isset($allowedClasses[$class])) {
                        $allowedClasses[$class] = $allClasses[$class];
                        $allowedClasses[$class][static::OPT_SCHEMA_MANDATORY] = $v->getMandatory();
                        $allowedClasses[$class][static::OPT_SCHEMA_ALLOWED_OPTIONS] = array();
                    }

                    if ($v->getMandatory()
                        && !$allowedClasses[$class][static::OPT_SCHEMA_MANDATORY]
                    ) {
                        // Mark class as mandatory if one of the options is mandatory
                        $allowedClasses[$class][static::OPT_SCHEMA_MANDATORY] = $v->getMandatory();
                    }

                    $allowedClasses[$class][static::OPT_SCHEMA_ALLOWED_OPTIONS][$code] = $allOptions[$code];
                }
            }

        } else {
            $allowedClasses = $allClasses;
        }

        if ($this->isDeliveryToPostOffice()
            && isset($allowedClasses[static::OPT_CLASS_WAY_TO_DELIVER])
        ) {
            // Remove "Way to deliver" class (it's not supported with D2PO)
            unset($allowedClasses[static::OPT_CLASS_WAY_TO_DELIVER]);
        }

        return $allowedClasses;
    }

    /**
     * Get Canada Post delivery service details
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    public function getDeliveryService()
    {
        return $this->getOrder()->getCapostDeliveryService();
    }

    // }}}

    // {{{ Change parcel status routine

    /**
     * Set status
     *
     * @param string $value Status code
     *
     * @return boolean
     */
    public function setStatus($value)
    {
        $oldStatus = ($this->status != $value) ? $this->status : null;

        $result = false;

        $statusHandler = $this->getStatusHandler($oldStatus, $value);

        if ($oldStatus
            && $this->isPersistent()
            && !empty($statusHandler)
        ) {
            $result = $this->{'changeStatus' . ucfirst($statusHandler)}();

            if ($result) {
                $this->oldStatus = $oldStatus;
                $this->status = $value;
            }

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param string $old Old status code
     * @param string $new New status code
     *
     * @return string
     */
    protected function getStatusHandler($old, $new)
    {
        return (isset(static::$statusHandlers[$old][$new])) ? static::$statusHandlers[$old][$new] : '';
    }

    /**
     * Status handler: "Created" to "Proposed" (void shipment)
     *
     * @return boolean
     */
    protected function changeStatusPropose()
    {
        $result = !$this->hasShipment();

        if ($this->canBeProposed()) {
            $isAllowedToRemove = true;

            if (static::QUOTE_TYPE_CONTRACTED == $this->getQuoteType()) {
                // Call API request if contracted shipment
                $isAllowedToRemove = $this->callApiVoidShipment();
            }

            if ($isAllowedToRemove) {
                $shipment = $this->getShipment();
                $this->setShipment(null);

                \XLite\Core\Database::getEM()->remove($shipment);
                \XLite\Core\Database::getEM()->flush();

                $result = true;
            }
        }

        return $result;
    }

    /**
     * Status handler: "Proposed" to "Created"
     *
     * @return boolean
     */
    protected function changeStatusCreate()
    {
        $capostConfig = \XLite\Core\Config::getInstance()->XC->CanadaPost;

        if (static::QUOTE_TYPE_NON_CONTRACTED == $capostConfig->quote_type) {
            // Call "Create Non-Contract Shipment" request
            $result = $this->callApiCreateNCShipment();

        } else {
            // Call "Create Shipment" request
            $result = $this->callApiCreateShipment();
        }

        // Update current qoute type
        $this->setQuoteType($capostConfig->quote_type);

        return $result;
    }

    /**
     * Status handler: "Created" to "Transmited"
     *
     * @return boolean
     */
    protected function changeStatusTransmit()
    {
        $result = false;

        if ($this->canBeTransmited()) {
            // Call "Transmit Shipments" request
            $result = $this->callApiTransmitShipment();
        }

        return $result;
    }

    /**
     * Check - is parcel/shipment can be created
     *
     * @return boolean
     */
    public function canBeCreated()
    {
        return (static::STATUS_PROPOSED == $this->getStatus());
    }

    /**
     * Check - is parcel/shipment can be proposed
     *
     * @return boolean
     */
    public function canBeProposed()
    {
        return (
            $this->hasShipment()
            && static::STATUS_CREATED == $this->getStatus()
            && (
                static::QUOTE_TYPE_NON_CONTRACTED == $this->getQuoteType()
                || (
                    static::QUOTE_TYPE_CONTRACTED == $this->getQuoteType()
                    && static::QUOTE_TYPE_CONTRACTED == \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type
                )
            )
        );
    }

    /**
     * Check - is parcel/shipment can be transmitted
     *
     * @return boolean
     */
    public function canBeTransmited()
    {
        return static::STATUS_CREATED === $this->getStatus()
            && $this->hasShipment()
            && static::QUOTE_TYPE_CONTRACTED === $this->getQuoteType()
            && static::QUOTE_TYPE_CONTRACTED === \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type;
    }

    // }}}

    /**
     * Check - parcel has shipment assigned or not
     *
     * @return boolean
     */
    public function hasShipment()
    {
        return null !== $this->getShipment();
    }

    /**
     * Check - parcel has item or not
     *
     * @return boolean
     */
    public function hasItems()
    {
        return 0 < $this->getItems()->count();
    }

    /**
     * Get total weight of the parcel's items (in store weight units)
     *
     * @return float
     */
    public function getWeight()
    {
        $weight = 0.00;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $weight += $item->getTotalWeight();
            }
        }

        return $weight;
    }

    /**
     * Get total weight of the parcel's items in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getWeightInKg($adjustFloatValue = false)
    {
        $weight = 0;

        if ($this->hasItems()) {
            foreach ($this->getItems() as $item) {
                $weight += $item->getTotalWeightInKg($adjustFloatValue);
            }
        }

        return $weight;
    }

    /**
     * Get maximum allowed weight of the parcel's box in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getBoxWeightInKg($adjustFloatValue = false)
    {
        // Convert weight from store units to KG (weight must be in KG)
        $weight = \XLite\Core\Converter::convertWeightUnits(
            $this->getBoxWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            'kg'
        );

        if ($adjustFloatValue) {
            // Adjust according to the XML element schema
            $weight = \XLite\Module\XC\CanadaPost\Core\Service\AService::adjustFloatValue($weight, 3, 0, 999.999);
        }

        return $weight;
    }

    /**
     * Check - is parcel overweight or not
     *
     * @return boolean
     */
    public function isOverWeight()
    {
        return ($this->getBoxWeight() < $this->getWeight());
    }

    /**
     * Check - is parcel editable or not
     *
     * @return boolean
     */
    public function isEditable()
    {
        return (
            static::STATUS_PROPOSED == $this->getStatus()
            && !$this->hasShipment()
        );
    }

    /**
     * Check - is parcel should be delivered to the Canada Post post office ot not
     *
     * @return boolean
     */
    public function isDeliveryToPostOffice()
    {
        return ($this->getOrder()->getCapostOffice());
    }

    /**
     * Check - is parcel locked ot not (API calls allowed or not)
     *
     * @return boolean
     */
    public function areAPICallsAllowed()
    {
        return (
            $this->hasItems()
            && !(
                static::STATUS_PROPOSED != $this->getStatus()
                && static::QUOTE_TYPE_CONTRACTED == $this->getQuoteType()
                && static::QUOTE_TYPE_NON_CONTRACTED == \XLite\Core\Config::getInstance()->XC->CanadaPost->quote_type
            )
        );
    }

    // {{{ Canada Post API calls

    /**
     * Get Canada Post API call errors
     *
     * @return null|array
     */
    public function getApiCallErrors()
    {
        return $this->apiCallErrors;
    }

    /**
     * Call Create Shipment request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateShipment()
    {
        $result = false;

        $shipment = $this->getShipment();

        if (null !== $shipment) {
            $this->apiCallErrors = array(
                'CALL_ERROR' => 'Parcel already has shipment data'
            );

        } else {
            $data = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->callCreateShipmentRequest($this);

            $result = $this->handleCreateShipmentApiCallResult(static::QUOTE_TYPE_CONTRACTED, $data);
        }

        return $result;
    }

    /**
     * Call Create Non-Contract Shipment request
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiCreateNCShipment()
    {
        $result = false;

        $shipment = $this->getShipment();

        if (null !== $shipment) {
            $this->apiCallErrors = array(
                'CALL_ERROR' => 'Parcel already has shipment data'
            );

        } else {
            $data = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->callCreateNCShipmentRequest($this);

            $result = $this->handleCreateShipmentApiCallResult(static::QUOTE_TYPE_NON_CONTRACTED, $data);
        }

        return $result;
    }

    /**
     * Handle return from "callApiCreateShipment" and "callApiCreateNCShipment" methods
     *
     * @param string                 $callType Call type
     * @param \XLite\Core\CommonCell $data     Returned value
     *
     * @return boolean
     */
    protected function handleCreateShipmentApiCallResult($callType, \XLite\Core\CommonCell $data)
    {
        $result = false;

        if (isset($data->errors)) {
            // Parse errors
            $this->apiCallErrors = $data->errors;

        } elseif (isset($data->shipmentId)) {
            // Parse valid response
            $shipment = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment();
            $shipment->setParcel($this);

            $this->setShipment($shipment);

            \XLite\Core\Database::getEM()->persist($shipment);

            foreach (array('shipmentId', 'shipmentStatus', 'trackingPin', 'returnTrackingPin', 'poNumber') as $_field) {
                $shipment->{'set' . \XLite\Core\Converter::convertToCamelCase($_field)}($data->{$_field});
            }

            if (isset($data->links)) {
                foreach ($data->links as $_link) {
                    $link = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Link();
                    $link->setShipment($shipment);

                    $shipment->addLink($link);

                    foreach (array('rel', 'href', 'mediaType', 'idx') as $_field) {
                        $link->{'set' . \XLite\Core\Converter::convertToCamelCase($_field)}($_link->{$_field});
                    }
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
    }

    /**
     * Call "Void Shipment" request (for Contracted shipments only)
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiVoidShipment()
    {
        $result = false;

        $data = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->callVoidShipmentRequest($this);

        if (isset($data->errors)) {
            // Save errors
            $this->apiCallErrors = $data->errors;

        } else {
            $result = true;
        }

        return $result;
    }

    /**
     * Call "Transmit Shipments" request (for Contracted shipments only)
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @return boolean
     */
    protected function callApiTransmitShipment()
    {
        $result = false;

        $data = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->callTransmitShipmentsRequest($this);

        if (isset($data->errors)) {
            // Save errors
            $this->apiCallErrors = $data->errors;

        } else {
            // Valid result

            sleep(2); // time to generate manifests

            $shipment = $this->getShipment();

            foreach ($data->links as $link) {
                $manifest = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest(
                    array(
                        'rel'        => $link->rel,
                        'href'       => $link->href,
                        'media_type' => $link->mediaType,
                    )
                );

                if (isset($link->idx)) {
                    $manifest->setIdx($link->idx);
                }

                \XLite\Core\Database::getEM()->persist($manifest);

                $shipment->addManifest($manifest);

                if (!$manifest->callApiGetManifest()
                    && $manifest->getApiCallErrors()
                ) {
                    // Error is occurred
                    if (null === $this->apiCallErrors) {
                        $this->apiCallErrors = array();
                    }

                    $this->apiCallErrors += $manifest->getApiCallErrors();
                }
            }

            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }

        return $result;
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
     * Set number
     *
     * @param integer $number
     * @return Parcel
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set quoteType
     *
     * @param string $quoteType
     * @return Parcel
     */
    public function setQuoteType($quoteType)
    {
        $this->quoteType = $quoteType;
        return $this;
    }

    /**
     * Get quoteType
     *
     * @return string 
     */
    public function getQuoteType()
    {
        return $this->quoteType;
    }

    /**
     * Set boxWeight
     *
     * @param decimal $boxWeight
     * @return Parcel
     */
    public function setBoxWeight($boxWeight)
    {
        $this->boxWeight = $boxWeight;
        return $this;
    }

    /**
     * Get boxWeight
     *
     * @return decimal 
     */
    public function getBoxWeight()
    {
        return $this->boxWeight;
    }

    /**
     * Set boxWidth
     *
     * @param decimal $boxWidth
     * @return Parcel
     */
    public function setBoxWidth($boxWidth)
    {
        $this->boxWidth = $boxWidth;
        return $this;
    }

    /**
     * Get boxWidth
     *
     * @return decimal 
     */
    public function getBoxWidth()
    {
        return $this->boxWidth;
    }

    /**
     * Set boxLength
     *
     * @param decimal $boxLength
     * @return Parcel
     */
    public function setBoxLength($boxLength)
    {
        $this->boxLength = $boxLength;
        return $this;
    }

    /**
     * Get boxLength
     *
     * @return decimal 
     */
    public function getBoxLength()
    {
        return $this->boxLength;
    }

    /**
     * Set boxHeight
     *
     * @param decimal $boxHeight
     * @return Parcel
     */
    public function setBoxHeight($boxHeight)
    {
        $this->boxHeight = $boxHeight;
        return $this;
    }

    /**
     * Get boxHeight
     *
     * @return decimal 
     */
    public function getBoxHeight()
    {
        return $this->boxHeight;
    }

    /**
     * Set isDocument
     *
     * @param boolean $isDocument
     * @return Parcel
     */
    public function setIsDocument($isDocument)
    {
        $this->isDocument = (boolean)$isDocument;
        return $this;
    }

    /**
     * Get isDocument
     *
     * @return boolean 
     */
    public function getIsDocument()
    {
        return $this->isDocument;
    }

    /**
     * Set isUnpackaged
     *
     * @param boolean $isUnpackaged
     * @return Parcel
     */
    public function setIsUnpackaged($isUnpackaged)
    {
        $this->isUnpackaged = (boolean)$isUnpackaged;
        return $this;
    }

    /**
     * Get isUnpackaged
     *
     * @return boolean 
     */
    public function getIsUnpackaged()
    {
        return $this->isUnpackaged;
    }

    /**
     * Set isMailingTube
     *
     * @param boolean $isMailingTube
     * @return Parcel
     */
    public function setIsMailingTube($isMailingTube)
    {
        $this->isMailingTube = (boolean)$isMailingTube;
        return $this;
    }

    /**
     * Get isMailingTube
     *
     * @return boolean 
     */
    public function getIsMailingTube()
    {
        return $this->isMailingTube;
    }

    /**
     * Set isOversized
     *
     * @param boolean $isOversized
     * @return Parcel
     */
    public function setIsOversized($isOversized)
    {
        $this->isOversized = (boolean)$isOversized;
        return $this;
    }

    /**
     * Get isOversized
     *
     * @return boolean 
     */
    public function getIsOversized()
    {
        return $this->isOversized;
    }

    /**
     * Set notifyOnShipment
     *
     * @param boolean $notifyOnShipment
     * @return Parcel
     */
    public function setNotifyOnShipment($notifyOnShipment)
    {
        $this->notifyOnShipment = (boolean)$notifyOnShipment;
        return $this;
    }

    /**
     * Get notifyOnShipment
     *
     * @return boolean 
     */
    public function getNotifyOnShipment()
    {
        return $this->notifyOnShipment;
    }

    /**
     * Set notifyOnException
     *
     * @param boolean $notifyOnException
     * @return Parcel
     */
    public function setNotifyOnException($notifyOnException)
    {
        $this->notifyOnException = (boolean)$notifyOnException;
        return $this;
    }

    /**
     * Get notifyOnException
     *
     * @return boolean 
     */
    public function getNotifyOnException()
    {
        return $this->notifyOnException;
    }

    /**
     * Set notifyOnDelivery
     *
     * @param boolean $notifyOnDelivery
     * @return Parcel
     */
    public function setNotifyOnDelivery($notifyOnDelivery)
    {
        $this->notifyOnDelivery = (boolean)$notifyOnDelivery;
        return $this;
    }

    /**
     * Get notifyOnDelivery
     *
     * @return boolean 
     */
    public function getNotifyOnDelivery()
    {
        return $this->notifyOnDelivery;
    }

    /**
     * Set optSignature
     *
     * @param boolean $optSignature
     * @return Parcel
     */
    public function setOptSignature($optSignature)
    {
        $this->optSignature = (boolean)$optSignature;
        return $this;
    }

    /**
     * Get optSignature
     *
     * @return boolean 
     */
    public function getOptSignature()
    {
        return $this->optSignature;
    }

    /**
     * Set optCoverage
     *
     * @param decimal $optCoverage
     * @return Parcel
     */
    public function setOptCoverage($optCoverage)
    {
        $this->optCoverage = $optCoverage;
        return $this;
    }

    /**
     * Get optCoverage
     *
     * @return decimal 
     */
    public function getOptCoverage()
    {
        return $this->optCoverage;
    }

    /**
     * Set optAgeProof
     *
     * @param string $optAgeProof
     * @return Parcel
     */
    public function setOptAgeProof($optAgeProof)
    {
        $this->optAgeProof = $optAgeProof;
        return $this;
    }

    /**
     * Get optAgeProof
     *
     * @return string 
     */
    public function getOptAgeProof()
    {
        return $this->optAgeProof;
    }

    /**
     * Set optWayToDeliver
     *
     * @param string $optWayToDeliver
     * @return Parcel
     */
    public function setOptWayToDeliver($optWayToDeliver)
    {
        $this->optWayToDeliver = $optWayToDeliver;
        return $this;
    }

    /**
     * Get optWayToDeliver
     *
     * @return string 
     */
    public function getOptWayToDeliver()
    {
        return $this->optWayToDeliver;
    }

    /**
     * Set optNonDelivery
     *
     * @param string $optNonDelivery
     * @return Parcel
     */
    public function setOptNonDelivery($optNonDelivery)
    {
        $this->optNonDelivery = $optNonDelivery;
        return $this;
    }

    /**
     * Get optNonDelivery
     *
     * @return string 
     */
    public function getOptNonDelivery()
    {
        return $this->optNonDelivery;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add items
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $items
     * @return Parcel
     */
    public function addItems(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
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
}
