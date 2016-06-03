<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
    }

}
