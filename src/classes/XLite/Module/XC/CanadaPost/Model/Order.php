<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model;

/**
 * Class represents an order
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Canada Post parcels (reference to the Canada Post parcels model)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\Parcel", mappedBy="order", cascade={"all"})
     */
    protected $capostParcels;

    /**
     * Reference to the Canada Post returns model
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\CanadaPost\Model\ProductsReturn", mappedBy="order", cascade={"all"})
     */
    protected $capostReturns;

    /**
     * Reference to the Canada Post post office model
     *
     * @var \XLite\Module\XC\CanadaPost\Model\Order\PostOffice
     *
     * @OneToOne (targetEntity="XLite\Module\XC\CanadaPost\Model\Order\PostOffice", mappedBy="order", cascade={"all"})
     */
    protected $capostOffice;

    // {{{ Service methods

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->capostParcels = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add Canada Post parcel to order
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $newParcel Canada Post parcel model
     *
     * @return void
     */
    public function addCapostParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $newParcel)
    {
        $newParcel->setOrder($this);
        $this->addCapostParcels($newParcel);
    }
    
    /**
     * Add Canada Post return
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post return model
     *
     * @return void
     */
    public function addCapostReturn(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        $return->setOrder($this);
        $this->addCapostReturns($return);
    }

    /**
     * Set post office
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\PostOffice $office Post office model (OPTIONAL)
     *
     * @return void
     */
    public function setCapostOffice(\XLite\Module\XC\CanadaPost\Model\Order\PostOffice $office = null)
    {
        if (null !== $office) {
            $office->setOrder($this);
        }

        $this->capostOffice = $office;
    }

    // }}}

    /**
     * Delete all Canada Post parcels
     *
     * @return void
     */
    public function removeAllCapostParcels()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\CanadaPost\Model\Order\Parcel');
        $repo->deleteInBatch($this->getCapostParcels());
    }

    /**
     * Calculated and create Canada Post parcels for the order
     *
     * @param boolean $replace Flag - replace order's parcels or not
     *
     * @return void
     */
    public function createCapostParcels($replace = false)
    {
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        $rawPackages = ($modifier && $modifier->getMethod() && $modifier->getMethod()->getProcessorObject())
            ? $modifier->getMethod()->getProcessorObject()->getPackages($modifier)
            : array();

        $capostConfig = \XLite\Core\Config::getInstance()->XC->CanadaPost;
        
        if ($replace) {
            // Remove order's parcels
            $this->removeAllCapostParcels();
        }

        if (!empty($rawPackages)
            && is_array($rawPackages)
        ) {
            foreach ($rawPackages as $packageIdx => $package) {
                $parcel = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel();

                $parcel->setOrder($this);
                
                $parcel->setNumber($packageIdx + 1);
                $parcel->setQuoteType($capostConfig->quote_type);
                
                // Set parcel box dimensions and weight
                foreach (array('weight', 'length', 'width', 'height') as $_param) {
                    $parcel->{'setBox' . \XLite\Core\Converter::convertToCamelCase($_param)}($package['box'][$_param]);
                }
                
                // Set parcel types (DEFAULT)
                foreach (array('document', 'unpackaged', 'mailing_tube', 'oversized') as $_param) {
                    $parcel->{'setIs' . \XLite\Core\Converter::convertToCamelCase($_param)}($capostConfig->{$_param});
                }
                
                // Set default parcel options (from the module settings)
                $optClasses = $parcel->getAllowedOptionClasses();

                foreach ($optClasses as $optClass => $classData) {
                    if ($parcel::OPT_CLASS_COVERAGE == $optClass) {
                        // Do not use default settings for "coverage"
                        continue;
                    }

                    // Set default option value from module settings
                    $value = (isset($capostConfig->{$optClass}))
                        ? $capostConfig->{$optClass}
                        : '';

                    if ($classData[$parcel::OPT_SCHEMA_MULTIPLE]
                        && $classData[$parcel::OPT_SCHEMA_MANDATORY]
                        && !isset($classData[$parcel::OPT_SCHEMA_ALLOWED_OPTIONS][$value])
                    ) {
                        // Set allowed option value
                        $value = array_shift(array_keys($classData[$parcel::OPT_SCHEMA_ALLOWED_OPTIONS]));
                    }

                    $parcel->{'setOpt' . \XLite\Core\Converter::convertToCamelCase($optClass)}($value);
                }

                // Add items to the parcel
                foreach ($package['items'] as $itemIdx => $item) {
                    $parcelItem = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item;

                    $parcelItem->setAmount($item['qty']);
                    $parcelItem->setOrderItem($this->getItemById($item['id']));
                    
                    // Assign parcel to the order
                    $parcel->addItem($parcelItem);
                }

                // Assign parcel to the order
                $this->addCapostParcel($parcel);

                \XLite\Core\Database::getEM()->persist($parcel);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Get order item by ID
     *
     * @param integer $id Order item ID
     *
     * @return \XLite\Model\OrderItem
     */
    public function getItemById($id)
    {
        foreach ($this->getItems() as $item) {
            if ($item->getItemId() == $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get shipping method code
     *
     * @return string
     */
    public function getCapostShippingMethodCode()
    {
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        $method = $modifier ? $modifier->getMethod() : null;
        
        return ($method && $method->getProcessor() === 'capost') ? $method->getCode() : '';
    }

    /**
     * Check - is shipping method is one of the Canada Post or not
     *
     * @return boolean
     */
    public function isCapostShippingMethod()
    {
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        $method = $modifier ? $modifier->getMethod() : null;

        return $method && $method->getProcessor() === 'capost';
    }

    /**
     * Return Canada Post parcels number
     *
     * @return integer
     */
    public function countCapostParcels()
    {
        return count($this->getCapostParcels());
    }
    
    /**
     * Check whether order has Canada Post parcels or not
     *
     * @return boolean
     */
    public function hasCapostParcels()
    {
        return (boolean) $this->countCapostParcels();
    }

    /**
     * Get Canada Post parcels tracking PINs
     *
     * @return array|null
     */
    public function getCapostTrackingPins()
    {
        $result = array();

        if ($this->hasCapostParcels()) {
            foreach ($this->getCapostParcels() as $parcel) {
                if ($parcel->hasShipment()) {
                    $result[] = array(
                        'shipment_id' => $parcel->getShipment()->getId(),
                        'pin_number'  => $parcel->getShipment()->getTrackingPin(),
                    );
                }
            }
        }

        return (empty($result)) ? null : $result;
    }

    /**
     * Get Canada Post delivery service details
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    public function getCapostDeliveryService()
    {
        $service = null;

        if ($this->isCapostShippingMethod()) {
            $serviceCode = $this->getSelectedShippingRate()->getMethod()->getCode();
            $destCountry = ($this->getCapostOffice())
                ? 'CA'
                : $this->getProfile()->getShippingAddress()->getCountry()->getCode();

            $service = \XLite\Core\Database::getRepo('XLite\Module\XC\CanadaPost\Model\DeliveryService')
                ->getServiceByCode($serviceCode, $destCountry);
        }

        return $service;
    }

    // {{{ Order fingerprints methods

    /**
     * Define fingerprint keys
     *
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        $list = parent::defineFingerprintKeys();
        $list[] = 'capostOfficeId';
        $list[] = 'capostShippingZipCode';

        // TODO: add 'capostOfficesHash' like for shippings?
        // TODO: add 'deliverToPO' like 'sameAddress' for billing address?

        return $list;
    }

    /**
     * Get fingerprint by 'capostOfficeId' key
     *
     * @return array
     */
    protected function getFingerprintByCapostOfficeId()
    {
        return $this->getCapostOffice()
            ? $this->getCapostOffice()->getOfficeId()
            : 0;
    }

    /**
     * Get fingerprint by 'capostShippingZipCode' field
     *
     * @return string
     */
    protected function getFingerprintByCapostShippingZipCode()
    {
        return ($this->getProfile() && $this->getProfile()->getShippingAddress())
            ? $this->getProfile()->getShippingAddress()->getZipcode()
            : '';
    }

    // }}}

    // {{{ Post Office

    /**
     * Common method to update cart/order
     *
     * @return void
     */
    public function updateOrder()
    {
        parent::updateOrder();
        
        $this->renewCapostOffice();
    }
   
    /**
     * Renew selected Canada Post post office
     *
     * @return void
     */
    public function renewCapostOffice()
    {
        $selectedOffice = $this->getCapostOffice();

        if (isset($selectedOffice)) {
            if ($this->isCapostOfficeApplicable($selectedOffice)) {
                $this->setCapostOffice($selectedOffice);

            } else {
                $this->setCapostOffice(null);
 
                \XLite\Core\Database::getEM()->remove($selectedOffice);
            }
        }
    }
    
    /**
     * Check - is Canada Post post office is applicable for this order or not
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\PostOffice $office Canada Post post office object
     *
     * @return boolean
     */
    public function isCapostOfficeApplicable(\XLite\Module\XC\CanadaPost\Model\Order\PostOffice $office)
    {
        return $this->isSelectedMethodSupportDeliveryToPO()
            && $this->isCapostOfficeAvailable($office->getOfficeId());
    }

    /**
     * Get selected shipping rate
     *
     * @return \XLite\Model\Shipping\Rate|null
     */
    public function getSelectedShippingRate()
    {
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return ($modifier && $modifier->getSelectedRate()) ? $modifier->getSelectedRate() : null;
    }

    /**
     * Get nearest Canada Post post offices that allowed deliver to them list (by the shipping address zipcode)
     *
     * @return array|null
     */
    public function getNearestCapostOffices()
    {
        return $this->getProfile() && $this->getProfile()->getShippingAddress()
            ? \XLite\Module\XC\CanadaPost\Core\Service\PostOffice::getInstance()
                ->callGetNearestPostOfficeByZipCode($this->getProfile()->getShippingAddress()->getZipcode(), true)
            : null;
    }

    /**
     * Check - is Canada Post post office is available
     *
     * @param string $officeId Canada Post post office ID
     *
     * @return boolean
     */
    public function isCapostOfficeAvailable($officeId)
    {
        $officesList = $this->getNearestCapostOffices();
        if (isset($officesList) && is_array($officesList)) {
            foreach ($officesList as $k => $v) {
                if ($v->getId() == $officeId) {
                    return true;
                }
            }
        }

        return false;
    }
    
    /**
     * Check - is selected shipping method support delivery to post office
     *
     * @return boolean
     */
    public function isSelectedMethodSupportDeliveryToPO()
    {
        $rate = $this->getSelectedShippingRate();

        return $rate
            && 'capost' === $rate->getMethod()->getProcessor()
            && in_array(
                $rate->getMethod()->getCode(),
                \XLite\Module\XC\CanadaPost\Core\API::getAllowedForDelivetyToPOMethodCodes(),
                true
            );
    }
    
    // }}}

    /**
     * Add capostParcels
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $capostParcels
     * @return Order
     */
    public function addCapostParcels(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $capostParcels)
    {
        $this->capostParcels[] = $capostParcels;
        return $this;
    }

    /**
     * Get capostParcels
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCapostParcels()
    {
        return $this->capostParcels;
    }

    /**
     * Add capostReturns
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $capostReturns
     * @return Order
     */
    public function addCapostReturns(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $capostReturns)
    {
        $this->capostReturns[] = $capostReturns;
        return $this;
    }

    /**
     * Get capostReturns
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCapostReturns()
    {
        return $this->capostReturns;
    }

    /**
     * Get capostOffice
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\PostOffice 
     */
    public function getCapostOffice()
    {
        return $this->capostOffice;
    }
}
