<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Checkout success page 
 *
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess implements \XLite\Base\IDecorator
{
    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            \XLite\Core\Session::getInstance()->xpc_order_create_profile
            && $this->getOrder()
            && $this->getOrder()->getOrderId()
            && (
                !\XLite\Core\Session::getInstance()->last_order_id
                || !$this->getCart()->getOrderId()
            )
        ) {

            $this->getCart()->setOrderId($this->getOrder()->getOrderId());
            \XLite\Core\Session::getInstance()->last_order_id = $this->getOrder()->getOrderId();

            parent::handleRequest();

            $this->getCart()->setOrderId(null);
            \XLite\Core\Session::getInstance()->last_order_id = null;
            \XLite\Core\Session::getInstance()->xpc_order_create_profile = null;

            // Cleanup fake carts from session
            \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::cleanupFakeCartsForProfile($this->getProfile());

        } else {
            parent::handleRequest();
        }
        
        \XLite\Core\Session::getInstance()->selectedCardId = null;
    }

}
