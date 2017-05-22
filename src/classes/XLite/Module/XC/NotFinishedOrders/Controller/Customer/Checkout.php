<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Controller\Customer;

use XLite\Module\XC\NotFinishedOrders\Main;

/**
 * Checkout controller
 */
abstract class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Order placement is success
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        $cart = $this->getCart();

        if ($cart && $cart->getNotFinishedOrder()) {
            $this->removeNotFinishedOrder($cart);
        }

        parent::processSucceed($fullProcess);
    }

    /**
     * Does the payment and order status handling
     */
    protected function doPayment()
    {
        $cart = $this->getCart();

        if (
            Main::isCreateOnPlaceOrder()
                && $this->isAllowedPlaceOrderNFO()
        ) {
            // If NFO should be created on 'Place order' action and current payment processor is not Offline,
            // then create NFO and reassign transaction on new order (cart)

            /** @var \XLite\Module\XC\NotFinishedOrders\Model\Cart $cart */
            $cart->processNotFinishedOrder(true);
        }

        parent::doPayment();
    }

    /**
     * Return true if specified processor allows to create NFO on place order action
     *
     * @return boolean
     */
    protected function isAllowedPlaceOrderNFO()
    {
        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();
        $processor = $transaction ? $transaction->getPaymentMethod()->getProcessor() : null;
        return $processor && !($processor instanceOf \XLite\Model\Payment\Processor\Offline);
    }

    /**
     * Remove not finished order
     *
     * @param \XLite\Model\Order $cart Not finished order to remove
     *
     * @return void
     */
    protected function removeNotFinishedOrder($cart)
    {
        $cart->removeNotFinishedOrder(true);
    }

    /**
     * Process cart profile
     *
     * @param boolean $doCloneProfile Clone profile flag
     *
     * @return boolean
     */
    protected function processCartProfile($doCloneProfile)
    {
        $cart = $this->getCart();

        if ($cart && $cart->isNotFinishedOrder()) {
            $doCloneProfile = false;
        }

        return parent::processCartProfile($doCloneProfile);
    }
}
