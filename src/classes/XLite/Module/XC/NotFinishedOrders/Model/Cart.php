<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Model;

use \XLite\Module\XC\NotFinishedOrders\Main;

/**
 * Class represents an order
 */
abstract class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    /**
     * Unset payment method
     *
     * @return void
     */
    public function unsetPaymentMethod()
    {
        $processed = false;

        $nfo = $this->getNotFinishedOrder();

        if ($nfo) {

            $transaction = $this->getFirstOpenPaymentTransaction();

            if ($transaction && !$transaction->isOpen()) {
                $this->getPaymentTransactions()->removeElement($transaction);
                $nfo->addPaymentTransactions($transaction);
                $transaction->setOrder($nfo);
                $processed = true;
            }
        }

        if (!$processed) {
            parent::unsetPaymentMethod();
        }
    }

    /**
     * Set payment status
     *
     * @param mixed $paymentStatus Payment status
     *
     * @return void
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        if ($this->isNeedProcessNFO($paymentStatus)) {
            // mark as not finished
            if (!$this->isNotFinishedOrder()) {
                $this->processNotFinishedOrder();
            }
        }
    }

    /**
     * Return true if NFO order should be created on order failure
     *
     * @param mixed $paymentStatus Payment status
     *
     * @return boolean
     */
    protected function isNeedProcessNFO($paymentStatus)
    {
        return (
                $paymentStatus == \XLite\Model\Order\Status\Payment::STATUS_DECLINED
                || $paymentStatus == \XLite\Model\Order\Status\Payment::STATUS_CANCELED
            )
            && (!$this->getShippingStatus() || (Main::isCreateOnPlaceOrder() && $this->isNotFinishedOrder()));
    }

    /**
     * Method to retrieve cart from either profile or session
     *
     * @return \XLite\Model\Cart
     */
    public static function tryRetrieveCart()
    {
        $cart = parent::tryRetrieveCart();

        if ($cart && $cart->isNotFinishedOrder()) {
            $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneBy(
                array(
                    'not_finished_order' => $cart->getOrderId()
                )
            );
        }

        return $cart;
    }

    /**
     * Method to turn not finished cart to order and generate new cart.
     * Return new cart model
     *
     * @param boolean $placeMode Flag: true - process NFO on place order action; false - on payment failure
     *
     * @return \XLite\Model\Cart
     */
    public function processNotFinishedOrder($placeMode = false)
    {
        if ($placeMode && $this->getNotFinishedOrder()) {
            // Do not create NFO on place order action if current cart already has NFO
            return $this;
        }

        if (!$placeMode) {
            $this->removeUnnecessaryTransaction();
        }

        $cart = $this->cloneEntity();

        $cart->stripDetails();

        $cart = $this->processNFOClonedEntity($cart);

        $this->removeNotFinishedOrder();

        $cart->setNotFinishedOrder($this);

        $this->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_NOT_FINISHED);
        $cart->setShippingStatus(null);

        $this->postprocessCart($cart, $placeMode);

        \XLite\Core\Database::getRepo('XLite\Model\Cart')->insert($cart);

        $this->setNewCart($cart);

        return $cart;
    }

    /**
     * Process not finished order
     *
     * @return void
     */
    protected function removeUnnecessaryTransaction()
    {
        foreach ($this->getPaymentTransactions() as $item) {
            if ($item->getStatus() === \XLite\Model\Payment\Transaction::STATUS_INITIALIZED) {
                $this->getPaymentTransactions()->removeElement($item);
                \XLite\Core\Database::getEM()->remove($item);
            }
        }
    }

    /**
     * Performs some operations on cart before flushing it to the database. Use this as an extension point
     *
     * @param \XLite\Model\Cart $cart
     * @param boolean $placeMode
     * @return mixed
     */
    protected function postprocessCart($cart, $placeMode)
    {
        if ($placeMode) {
            $transaction = $this->getFirstOpenPaymentTransaction();
            $transaction->setOrder($cart);
            $cart->addPaymentTransactions($transaction);
        }

        return $cart;
    }

    /**
     * Process cloned not finished order
     *
     * @param \XLite\Model\Order $cart Cloned order entity
     *
     * @return \XLite\Model\Order
     */
    protected function processNFOClonedEntity($cart)
    {
        $userProfile = $this->getProfile();

        if ($userProfile) {

            if (!$userProfile->getAnonymous()) {
                $this->setProfile($cart->getProfile());
                $cart->setProfile($userProfile);
                $this->getProfile()->setOrder($this);

            } elseif ($cart->getProfile()) {
                // We should save password too as it it reset in cloned profile
                $cart->getProfile()->setPassword($userProfile->getPassword());
            }
        }

        return $cart;
    }

    /**
     * Set new cart object
     *
     * @param \XLite\Model\Order $object Cart object to set
     *
     * @return void
     */
    protected function setNewCart(\XLite\Model\Order $object)
    {
        \XLite\Model\Cart::setObject($object);
    }

    /**
     * Removes unwanted data on cart cloning
     *
     * @return boolean
     */
    protected function stripDetails()
    {
        $this->setNotFinishedOrder(null);
        foreach ($this->getPaymentTransactions() as $item) {
            $this->getPaymentTransactions()->removeElement($item);
        }

        foreach ($this->getEvents() as $item) {
            $this->getEvents()->removeElement($item);
        }

        foreach ($this->getDetails() as $item) {
            $this->getDetails()->removeElement($item);
        }
    }

    /**
     * Retrieves order data from source
     *
     * @param \XLite\Model\Order $entity Source entity
     *
     * @return boolean
     */
    protected function insertDetailsFrom($entity)
    {
        $this->setAdminNotes($entity->getAdminNotes());
        foreach ($entity->getPaymentTransactions() as $item) {
            if ($item->getStatus() !== \XLite\Model\Payment\Transaction::STATUS_INITIALIZED) {
                $this->addPaymentTransactions($item);
                $item->setOrder($this);
                $entity->getPaymentTransactions()->removeElement($item);
            }
        }

        foreach ($entity->getEvents() as $item) {
            $this->addEvents($item);
            $item->setOrder($this);
            $entity->getEvents()->removeElement($item);
        }

        foreach ($entity->getDetails() as $item) {
            $this->addDetails($item);
            $item->setOrder($this);
            $entity->getDetails()->removeElement($item);
        }
    }

    /**
     * Check if the cart is isOpen
     *
     * @return boolean
     */
    public function isOpen()
    {
        return parent::isOpen() && !$this->isNotFinishedOrder();
    }

    /**
     * Closes cart if it is not finished order
     *
     * @return boolean
     */
    public function closeNotFinishedOrder()
    {
        $this->tryClose();

        $cart = $this->closeLinkedOrder();

        if ($cart && 'Y' === \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->clear_cart_on_order_change) {
            $cart->clear();
        }
    }

    /**
     * Remove relation between this order and other order linked to this by not_finished_order_id
     *
     * @return \XLite\Model\Order
     */
    protected function closeLinkedOrder()
    {
        $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneBy(
            array(
                'not_finished_order' => $this->getOrderId()
            )
        );

        if ($cart) {
            $cart->setNotFinishedOrder(null);
            if ($this->isQueued()) {
                // NFO is created on place order action: Save payment transactions related to the cart to this NFO
                foreach ($cart->getPaymentTransactions() as $t) {
                    $t->setOrder($this);
                    $cart->getPaymentTransactions()->removeElement($t);
                    $this->addPaymentTransactions($t);
                }
            }
        }

        return $cart;
    }

    /**
     * Removes not finished order if it is present and takes its history back in cart
     *
     * @param boolean $force Force removing of NFO flag OPTIONAL
     *
     * @return void
     */
    public function removeNotFinishedOrder($force = false)
    {
        $order = $this->getNotFinishedOrder();

        if ($order && $this->canRemoveNotFinishedOrder($order)) {
            $this->insertDetailsFrom($order);

            \XLite\Core\Database::getEM()->remove($order);
        }

        $this->setNotFinishedOrder(null);
    }

    /**
     * Return true if NFO can be removed
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function canRemoveNotFinishedOrder($order)
    {
        return !$order->isExpiredTTL()
            || (
                Main::isCreateOnPlaceOrder()
                && $order->isQueued()
            );
    }
}
