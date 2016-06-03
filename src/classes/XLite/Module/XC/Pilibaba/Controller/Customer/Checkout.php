<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Controller\Customer;

use XLite\Module\XC\Pilibaba;

/**
 * Checkout controller
 */
abstract class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Get pilibaba default address
     *
     * @return \XLite\Model\Address
     */
    protected function getDefaultAddress()
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        $addressObject = unserialize(
            base64_decode(
                Pilibaba\Main::getPaymentMethod()->getSetting('warehouse')
            )
        );
        $result = \XLite\Model\Address::createDefaultShippingAddress();

        if ($addressObject instanceof \PilipayWarehouseAddress) {
            $result = \XLite\Module\XC\Pilibaba\Logic\PilipayWarehouseAddressConverter::convertAddress(
                $addressObject,
                \XLite\Model\Address::createDefaultShippingAddress()
            );
        }

        return $result;
    }
    /**
     * doActionStartExpressCheckout
     *
     * @return void
     */
    protected function doActionStartPilibabaCheckout()
    {
        $paymentMethod = Pilibaba\Main::getPaymentMethod();

        $this->getCart()->setPaymentMethod($paymentMethod);

        $profile = $this->getCartProfile();
        \XLite\Core\Database::getEM()->flush($profile);

        $this->processCartProfile(true);

        $address = $this->getDefaultAddress();
        $address->setProfile($profile);
        $address->setIsBilling(true);
        $address->setIsShipping(true);
        $address->setIsWork(true);
        $profile->addAddresses($address);

        \XLite\Core\Database::getEM()->persist($address);

        $shippingMethod = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findOneBy(
            array(
                'processor' => Pilibaba\Model\Shipping\Processor\Pilibaba::PROCESSOR_ID,
                'code'      => 'PilibabaShipping',
            )
        );

        if ($shippingMethod) {
            $this->getCart()->setLastShippingId($shippingMethod->getMethodId());
            $this->getCart()->setShippingId($shippingMethod->getMethodId());
        }
        $this->getCart()->updateCart();
        \XLite\Core\Database::getEM()->flush();

        $this->doActionCheckout();
    }

    /**
     * Check checkout action accessibility
     *
     * @return boolean
     */
    public function checkCheckoutAction()
    {
        return $this->getCart()->getPaymentMethod() && $this->getCart()->getPaymentMethod()->getServiceName() === 'Pilibaba'
            ? true
            : parent::checkCheckoutAction();
    }
}
