<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Address;

/**
 * Order address modification
 */
class OrderModify extends \XLite\View\AView
{

    /**
     * Address (local cache)
     * 
     * @var   \XLite\Model\Address
     */
    protected $address;

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'address/order/style.css';

        return $list;
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'address/order/modify_address.twig';
    }

    /**
     * Get model template 
     * 
     * @return string
     */
    protected function getModelTemplate()
    {
        return 'address/order/model.twig';
    }

    /**
     * Get name 
     * 
     * @return string
     */
    protected function getName()
    {
        $profile = $this->getOrder()->getProfile();
        $address = $profile->getShippingAddress() ?: $profile->getBillingAddress();

        return $address ? $address->getName() : 'n/a';
    }

    /**
     * Get email 
     * 
     * @return string
     */
    protected function getEmail()
    {
        return $this->getOrder()->getProfile()->getLogin();
    }

    /**
     * Get billing address
     *
     * @return \XLite\Model\Address
     */
    protected function getBillingAddress()
    {
        return $this->getOrder()->getProfile()->getBillingAddress() ?: $this->getOrder()->getProfile()->getShippingAddress();
    }

    /**
     * Get shipping address 
     * 
     * @return \XLite\Model\Address
     */
    protected function getShippingAddress()
    {
        return $this->getOrder()->getProfile()->getShippingAddress() ?: $this->getOrder()->getProfile()->getBillingAddress();
    }

    /**
     * Check - billing address is same shipping address
     * 
     * @return boolean
     */
    protected function isSameShipping()
    {
        return $this->getShippingAddress()->getAddressId() == $this->getBillingAddress()->getAddressId();
    }

    /**
     * Get container attributes 
     * 
     * @return array
     */
    protected function getContainerAttributes()
    {
        $attributes = array(
            'class' => array('order-address-dialog'),
        );

        switch (\XLite\Core\Request::getInstance()->type) {
            case 'shippingAddress':
                $attributes['class'][] = 'shipping-section';
                break;

            case 'billingAddress':
                $attributes['class'][] = 'billing-section';
                break;

            default:
        }

        if ($this->isSameShipping()) {
            $attributes['class'][] = 'same-address';
        }

        return $attributes;
    }

    /**
     * Get billing container attributes
     *
     * @return array
     */
    protected function getBillingContainerAttributes()
    {
        $attributes = array(
            'class' => array('address-box', 'billing', 'clearfix'),
        );

        if ('shippingAddress' == \XLite\Core\Request::getInstance()->type) {
            $attributes['class'][] = 'collapsed';
        }

        return $attributes;
    }

    /**
     * Get billing container attributes
     *
     * @return array
     */
    protected function getShippingContainerAttributes()
    {
        $attributes = array(
            'class' => array('address-box', 'shipping', 'clearfix'),
        );

        if ('billingAddress' == \XLite\Core\Request::getInstance()->type) {
            $attributes['class'][] = 'collapsed';
        }

        return $attributes;
    }

    /**
     * Checks whether display 'Address book' button or not
     *
     * @return boolean
     */
    protected function isDisplayAddressButton()
    {
        return 1 < count($this->getOrder()->getAddresses());
    }
}
