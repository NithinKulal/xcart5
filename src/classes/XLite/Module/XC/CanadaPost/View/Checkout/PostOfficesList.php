<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Checkout;

/**
 * Canada Post offices list
 *
 * @ListChild (list="checkout.shipping.selected.sub.payment", weight="210")
 */
class PostOfficesList extends \XLite\View\AView
{
    /**
     * Shipping modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $shippingModifier;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/CanadaPost/checkout/steps/shipping/parts/post_offices.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/checkout/steps/shipping/parts/post_offices.css';

        return $list;
    }
    
    /**
     * Check - is shipping method is support "Delivery to Post Office" feature
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method object
     *
     * @return boolean
     */
    protected function isMethodSupportDeliveryToPO(\XLite\Model\Shipping\Method $method)
    {
        return (
            'capost' == $method->getProcessor()
            && in_array($method->getCode(), \XLite\Module\XC\CanadaPost\Core\API::getAllowedForDelivetyToPOMethodCodes())
        );
    }
    
    /**
     * Check - is selected shipping methods is support "Delivery to Post Office" feature
     *
     * @return boolean
     */
    protected function isSelectedMethodSupportDeliveryToPO()
    {
        $method = $this->getSelectedMethod();

        return (
            isset($method)
            && $this->isMethodSupportDeliveryToPO($method)
        );
    }

    /**
     * Get selected rate 
     * 
     * @return \XLite\Model\Shipping\Method
     */
    protected function getSelectedMethod()
    {
        return ($this->getShippingModifier() && $this->getShippingModifier()->getSelectedRate())
            ? $this->getShippingModifier()->getSelectedRate()->getMethod()
            : null;
    }

    /**
     * Check - shipping rates is available or not
     *
     * @return boolean
     */
    protected function isShippingAvailable()
    {
        return (
            $this->getShippingModifier() 
            && $this->getShippingModifier()->isRatesExists() 
            && $this->getCart()->getProfile()
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (
            parent::isVisible()
            && $this->isDeliverToPOEnabled()
            && $this->isShippingAvailable()
        );
    }
    
    /**
     * Check - is offices list is visible (on widget load) or not
     *
     * @return boolean
     */
    protected function isOfficesListVisible()
    {
        return $this->hasCartCapostOffice();
    }
    
    /**
     * Check - has cart selected Canada Post post office or not
     *
     * @return boolean
     */
    protected function hasCartCapostOffice()
    {
        return ($this->getCart()->getCapostOffice()) ? true : false;
    }

    /**
     * Check - is post office selected or not
     *
     * @param \XLite\Module\XC\CanadaPost\Model\PostOffice $office Canada Post post office temp model
     *
     * @return boolean
     */
    protected function isCapostOfficeSelected(\XLite\Module\XC\CanadaPost\Model\PostOffice $office)
    {
        return ($office->getId() && $office->getId() == $this->getSelectedOfficeId());
    }
    
    /**
     * Get selected post office ID
     *
     * @return string|null
     */
    protected function getSelectedOfficeId()
    {
        return ($this->hasCartCapostOffice()) ? $this->getCart()->getCapostOffice()->getOfficeId() : null;
    }
    
    /**
     * Check - is delivery to post office enabled
     *
     * @return boolean
     */
    protected function isDeliverToPOEnabled()
    {
        return \XLite\Core\Config::getInstance()->XC->CanadaPost->deliver_to_po_enabled;
    }
    
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/checkout/steps/shipping/parts/post_offices.twig';
    }

    /**
     * Get shipping rates
     *
     * @return array
     */
    protected function getRates()
    {
        return $this->getShippingModifier()->getRates();
    }

    /**
     * Get shipping modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getShippingModifier()
    {
        if (!isset($this->shippingModifier)) {
            $this->shippingModifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->shippingModifier;
    }
}
