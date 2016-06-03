<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Admin;

/**
 * Order page controller (additional methods for "shipments" page)
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_CAPOST_SHIPMENTS = 'capost_shipments';

    /**
     * Initialize controller
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (
            static::PAGE_CAPOST_SHIPMENTS == \XLite\Core\Request::getInstance()->page
            && $this->getOrder()->isCapostShippingMethod()
            && !$this->getOrder()->hasCapostParcels()
        ) {
            // Create and save Canda Post parcels (first time only)
            $this->getOrder()->createCapostParcels();
        }
    }
    
    /**
     * Update parcels data and/or move items
     *
     * @return void
     */
    protected function doActionCapostUpdateParcel()
    {
        // Update parcels data
        $parcelsData = \XLite\Core\Request::getInstance()->parcelsData;

        if (
            isset($parcelsData) 
            && is_array($parcelsData)
        ) {
            // Update parcels characteristics and options
            foreach ($parcelsData as $parcelId => $newData) {

                $parcel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel')->find($parcelId);

                if (
                    isset($parcel)
                    && $parcel->isEditable()
                ) {
                    $parcel->map($newData);
                }
            }

            \XLite\Core\Database::getEM()->flush();
        }

        // Move items
        $moveItems = \XLite\Core\Request::getInstance()->moveItems;

        if (
            !empty($moveItems) 
            && is_array($moveItems)
        ) {
            foreach ($moveItems as $itemId => $itemData) {

                $itemData['amount'] = intval($itemData['amount']);

                if (
                    $itemData['amount'] < 1 
                    || empty($itemData['parcelId'])
                ) {
                    continue;
                }

                \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item')
                    ->moveItem($itemId, $itemData['amount'], $itemData['parcelId']);
            }
        }
        
        \XLite\Core\TopMessage::addInfo(
            'Parcels have been successfully updated'
        );
    }

    /**
     * Do create shipment action
     *
     * @return void
     */
    protected function doActionCapostCreateShipment()
    {
        $parcelId = intval(\XLite\Core\Request::getInstance()->parcel_id);

        $parcel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel')->find($parcelId);

        if (isset($parcel)) {

            if ($parcel->canBeCreated()) {
            
                // Change parcel status to call API requests
                $result = $parcel->setStatus('C');
    
                $errors = $parcel->getApiCallErrors();
        
                if ($result) {
                    
                    if (isset($errors)) {
    
                        \XLite\Core\TopMessage::addWarning(
                            'Shipment has been created with errors'
                        );
    
                    } else {
                    
                        \XLite\Core\TopMessage::addInfo(
                            'Shipment has been created successfully'
                        );
                    }
                } 
                
                if (isset($errors)) {
                    foreach ($errors as $errCode => $err) {
                        $label = '[' . $errCode . '] ' . $err;
                        \XLite\Core\TopMessage::addError($label);
                    }
                }

                \XLite\Core\Database::getEM()->flush();

            } else {
                
                \XLite\Core\TopMessage::addError('Shipment cannot be created');

            }
        }
    }
    
    /**
     * Do void shipment action
     *
     * @return void
     */
    protected function doActionCapostVoidShipment()
    {
        $parcelId = intval(\XLite\Core\Request::getInstance()->parcel_id);

        $parcel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel')->find($parcelId);

        if (isset($parcel)) {

            if ($parcel->canBeProposed()) {
            
                $result = $parcel->setStatus('P');
                
                $errors = $parcel->getApiCallErrors();

                if ($result) {

                    if (isset($errors)) {

                        \XLite\Core\TopMessage::addWarning(
                            'Shipment has been voided with errors'
                        );
    
                    } else {
    
                        \XLite\Core\TopMessage::addInfo(
                            'Shipment has been voided successfully'
                        );
                    }
                }

                if (isset($errors)) {
                    foreach ($errors as $errCode => $err) {
                        $label = '[' . $errCode . '] ' . $err;
                        \XLite\Core\TopMessage::addError($label);
                    }
                }

                \XLite\Core\Database::getEM()->flush();

            } else {

                \XLite\Core\TopMessage::addError('Shipment cannot be voided');
            }
        }
    }
    
    /**
     * Do transmit shipment action
     *
     * @return void
     */
    protected function doActionCapostTransmitShipment()
    {
        $parcelId = intval(\XLite\Core\Request::getInstance()->parcel_id);

        $parcel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel')->find($parcelId);

        if (isset($parcel)) {
            
            if ($parcel->canBeTransmited()) {

                $result = $parcel->setStatus('T');

                $errors = $parcel->getApiCallErrors();

                if ($result) {

                    if (isset($errors)) {

                        \XLite\Core\TopMessage::addWarning(
                            'Shipment has been tranmitted with errors'
                        );
    
                    } else {
    
                        \XLite\Core\TopMessage::addInfo(
                            'Shipment has been transmitted successfully'
                        );
                    }
                }

                if (isset($errors)) {
                    foreach ($errors as $errCode => $err) {
                        $label = '[' . $errCode . '] ' . $err;
                        \XLite\Core\TopMessage::addError($label);
                    }
                }

                \XLite\Core\Database::getEM()->flush();

            } else {

                \XLite\Core\TopMessage::addError('Shipment cannot be transmitted');
            }
        }
    }

    // }}}

    /**
     * Get order's Canada Post parcels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCapostOrderParcels()
    {
        return $this->getOrder()->getCapostParcels();
    }

    /**
     * Get delivery service details
     *
     * @return \XLite\Module\XC\CanadaPost\Model\DeliveryService|null
     */
    public function getCapostDeliveryService()
    {
        return $this->getOrder()->getCapostDeliveryService();
    }

    // {{{ Add "Shipments" tab to the order details page

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        
        if ($this->getOrder()
            && $this->getOrder()->isCapostShippingMethod()
        ) {
            $list[static::PAGE_CAPOST_SHIPMENTS] = static::t('Shipments');
        }

        return $list;
    }
    
    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        
        if (
            $this->getOrder() 
            && $this->getOrder()->isCapostShippingMethod()
        ) {
            $list[static::PAGE_CAPOST_SHIPMENTS] = 'modules/XC/CanadaPost/shipments/page.twig';
        }

        return $list;
    }

    // }}}
}
