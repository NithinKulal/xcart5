<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Customer;

/**
 * Checkout controller
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Set Canada Post post office
     *
     * @return void
     */
    protected function doActionCapostOffice()
    {
        if ($this->getCapostDeliverToPO()) {
            
            $officeId = $this->getCapostOfficeId();

            if (empty($officeId)) {

                // Remove Canda Post post office (if had been selected)
                $this->removeCapostOffice();

            } else {

                // Set selected Canada Post post office
                $this->assignCapostOffice($officeId);
            }

        } else {
            
            // Remove Canda Post post office (if selected)
            $this->removeCapostOffice();
        }
    }
    
    /**
     * Get Canada Post office ID
     *
     * @return string
     */
    protected function getCapostOfficeId()
    {
        return strval(\XLite\Core\Request::getInstance()->capostOfficeId);
    }
    
    /**
     * Get option: capostDeliverToPO
     *
     * @return boolean
     */
    protected function getCapostDeliverToPO()
    {
        return (bool) \XLite\Core\Request::getInstance()->capostDeliverToPO;
    }

    /**
     * Assign given Canada Post post office to the cart
     *
     * @param string $officeId Canada Post post office ID
     *
     * @return boolean
     */
    protected function assignCapostOffice($officeId)
    {
        $officeRaw = $this->getCapostOfficeDetails($officeId);

        $result = false;

        if (isset($officeRaw)) {

            $office = $this->getCart()->getCapostOffice();

            if (!isset($office)) {

                // Create new post office object

                $office = new \XLite\Module\XC\CanadaPost\Model\Order\PostOffice();

                $office->setOrder($this->getCart());
                $this->getCart()->setCapostOffice($office);

                \XLite\Core\Database::getEM()->persist($office);
            }

            // Update post office details
            $office->setOfficeId($officeRaw->getId());
            
            $commonFields = array(
                'name', 'location', 'distance', 'bilingualDesignation', 'city', 
                'latitude', 'longitude', 'postalCode', 'province', 'officeAddress',
            );

            foreach ($commonFields as $k => $v) {
                $field = \XLite\Core\Converter::convertToCamelCase($v);
                $office->{'set' . $field}($officeRaw->{'get' . $field}());
            }

            $this->updateCart();
 
            \XLite\Core\Database::getEM()->flush();

            $result = true;
        }
        
        return $result;
    }
    
    /**
     * Remove assigned Canada Post post office
     *
     * @return void
     */
    protected function removeCapostOffice()
    {
        $office = $this->getCart()->getCapostOffice();

        if (isset($office)) {

            $this->getCart()->setCapostOffice(null);

            \XLite\Core\Database::getEM()->remove($office);

            $this->updateCart();
 
            \XLite\Core\Database::getEM()->flush();

        }
    }

    // {{{ Common methods

    /**
     * Get nearest Canada Post post offices list (by the shipping address zipcode)
     *
     * @return array|null
     */
    public function getNearestCapostOffices()
    {
        return $this->getCart()->getNearestCapostOffices();
    }
   
    /**
     * Get Canada Post office details
     *
     * @param string $officeId Canada Post post office ID
     *
     * @return \XLite\Module\XC\CanadaPost\Model\PostOffice|null
     */
    public function getCapostOfficeDetails($officeId)
    {
        $office = null;

        $officesList = $this->getNearestCapostOffices();
        
        if (
            isset($officesList) 
            && is_array($officesList)
        ) {
            foreach ($officesList as $k => $v) {
                if ($v->getId() == $officeId) {
                    $office = $v;
                    break;
                }
            }
        }
        
        return $office;
    }
    
    // }}}
}
