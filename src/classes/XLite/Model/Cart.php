<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Cart
 *
 * @Entity
 * @HasLifecycleCallbacks
 */
class Cart extends \XLite\Model\Order
{
    /**
     * Cart renew period
     */
    const RENEW_PERIOD = 3600;

    /**
     * Array of instances for all derived classes
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * Flag to ignore long calculation for speed up purposes
     *
     * @var boolean
     */
    protected static $ignoreLongCalculations = false;

    /**
     * Method to access a singleton
     *
     * @param boolean $doCalculate Flag for cart recalculation OPTIONAL
     *
     * @return \XLite\Model\Cart
     */
    public static function getInstance($doCalculate = true)
    {
        $className = get_called_class();

        // Create new instance of the object (if it is not already created)
        if (!isset(static::$instances[$className])) {
            $auth = \XLite\Core\Auth::getInstance();

            $cart = static::tryRetrieveCart();

            if (!isset($cart)) {
                // Cart not found - create a new instance
                $cart = new $className();
                $cart->initializeCart();
            }

            static::$instances[$className] = $cart;

            if ($auth->isLogged()
                && (!$cart->getProfile()
                    || $auth->getProfile()->getProfileId() != $cart->getProfile()->getProfileId()
                )
            ) {
                $cart->setProfile($auth->getProfile());
                $cart->setOrigProfile($auth->getProfile());
            }

            // Check login state
            if (\XLite\Core\Session::getInstance()->lastLoginUnique === null
                && $cart->getProfile()
                && $cart->getProfile()->getAnonymous()
                && $cart->getProfile()->getLogin()
            ) {
                $tmpProfile = new \XLite\Model\Profile;
                $tmpProfile->setProfileId(0);
                $tmpProfile->setLogin($cart->getProfile()->getLogin());

                $exists = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                    ->checkRegisteredUserWithSameLogin($tmpProfile);

                \XLite\Core\Session::getInstance()->lastLoginUnique = !$exists;
            }

            if ($cart->isPersistent()) {
                if (!$doCalculate) {
                    $cart->setIgnoreLongCalculations();
                }

                if (!$cart->isIgnoreLongCalculations()
                    && ($cart instanceof \XLite\Model\Cart
                        || (\XLite\Core\Converter::time() - static::RENEW_PERIOD) > $cart->getLastRenewDate()
                    )
                ) {
                    $cart->renew();

                } else {
                    $cart->calculate();
                }

                if ($cart->getPaymentMethod() && !$cart->getPaymentMethod()->isEnabled()) {
                    $cart->renewPaymentMethod();
                }

                $cart->renewSoft();

                \XLite\Core\Session::getInstance()->order_id = $cart->getOrderId();
            }
        }

        return static::$instances[$className];
    }

    /**
     * Add item to order
     *
     * @param \XLite\Model\OrderItem $newItem Item to add
     *
     * @return boolean
     */
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $this->renewUpdatedTime();
        return parent::addItem($newItem);
    }

    /**
     * Check if recently updated
     *
     * @return boolean
     */
    public function isRecentlyUpdated()
    {
        return (bool)$this->getUpdatedTime();
    }

    /**
     * Check if given payment method can be applied to the order
     * 
     * @param  \XLite\Model\Payment\Method  $method
     * @return boolean
     */
    protected function isPaymentMethodIsApplicable($method)
    {
        return parent::isPaymentMethodIsApplicable($method)
            && $method->isEnabled();
    }

    /**
     * Return updated time
     *
     * @return integer
     */
    public function getUpdatedTime()
    {
        return \XLite\Core\Session::getInstance()->cartUpdatedTime;
    }

    /**
     * Renew updated time
     */
    public function renewUpdatedTime()
    {
        \XLite\Core\Session::getInstance()->cartUpdatedTime = \XLite\Core\Converter::getInstance()->time();
    }

    /**
     * Set updated time to 0
     */
    public function unsetUpdatedTime()
    {
        \XLite\Core\Session::getInstance()->cartUpdatedTime = 0;
    }

    /**
     * Set object instance
     *
     * @param \XLite\Model\Order $object Cart
     *
     * @return void
     */
    public static function setObject(\XLite\Model\Order $object)
    {
        $className = get_called_class();
        static::$instances[$className] = $object;
        \XLite\Core\Session::getInstance()->order_id = $object->getOrderId();
    }

    /**
     * Method to retrieve cart from either profile or session
     *
     * @return \XLite\Model\Cart
     */
    public static function tryRetrieveCart()
    {
        $auth = \XLite\Core\Auth::getInstance();
        $cart = null;

        if ($auth->isLogged()) {
            // Try to find cart of logged in user
            $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneByProfile($auth->getProfile());
        }

        if (empty($cart)) {
            // Try to get cart from session
            $orderId = \XLite\Core\Session::getInstance()->order_id;
            if ($orderId) {
                $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->findOneForCustomer($orderId);

                // Forget cart if cart is order
                if ($cart && !$cart->hasCartStatus()) {
                    unset(\XLite\Core\Session::getInstance()->order_id, $cart);
                }
            }
        }

        return $cart;
    }

    /**
     * Try close
     *
     * @param \XLite\Controller\Customer\Checkout $controller Controller OPTIONAL
     *
     * @return boolean
     */
    public function tryClose(\XLite\Controller\Customer\Checkout $controller = null)
    {
        $result = false;

        if (!$this->isOpen()) {
            if (!$controller) {
                \XLite\Model\Cart::setObject($this);

                if ($this instanceof \XLite\Model\Cart) {
                    $this->assignOrderNumber();
                }
            }

            $paymentStatus = $this->getCalculatedPaymentStatus(true);
            $this->setPaymentStatus($paymentStatus);

            if (!$controller) {
                $controller = new \XLite\Controller\Customer\Checkout();
            }
            $controller->processSucceed();

            $result = true;
        }

        return $result;
    }

    /**
     * Get ignoreLongCalculations flag value
     *
     * @return boolean
     */
    public function isIgnoreLongCalculations()
    {
        return static::$ignoreLongCalculations;
    }

    /**
     * Set ignoreLongCalculations flag value
     *
     * @return boolean
     */
    public function setIgnoreLongCalculations()
    {
        return static::$ignoreLongCalculations = true;
    }

    /**
     * Calculate order
     *
     * @return void
     */
    public function calculate()
    {
        if ($this->isPersistent()) {
            parent::calculate();

            \XLite\Core\Session::getInstance()->lastCartInitialCalculate = \XLite\Core\Converter::time();
        }
    }

    /**
     * Order number is assigned during the pay process
     * It must be kept during the checkout session
     *
     * @return void
     */
    public function assignOrderNumber()
    {
        if (!$this->getOrderNumber()) {
            $this->setOrderNumber(
                \XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber()
            );

            if ($this->isFlushOnOrderNumberAssign()) {
                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    /**
     * Should we flush in order assign
     *
     * @return boolen
     */
    public function isFlushOnOrderNumberAssign()
    {
        return true;
    }

    /**
     * Calculate order with the subtotal calculation only
     *
     * @return void
     */
    public function calculateInitial()
    {
        $this->reinitializeCurrency();
    }

    /**
     * Prepare order before save data operation
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        $this->setDate(\XLite\Core\Converter::time());
    }

    /**
     * Prepare order before create entity
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        $this->setLastRenewDate(\XLite\Core\Converter::time());
    }

    /**
     * Clear cart (remove cart items)
     *
     * @return void
     */
    public function clear()
    {
        foreach ($this->getItems() as $item) {
            \XLite\Core\Database::getEM()->remove($item);
        }

        $this->getItems()->clear();
    }


    /**
     * Checks whether a product is in the cart
     *
     * @param integer $productId ID of the product to look for
     *
     * @return boolean
     */
    public function isProductAdded($productId)
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();

            if ($product && $product->getProductId() == $productId) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get cart items
     *
     * @return array
     */
    public function getItems()
    {
        $items = parent::getItems();

        if (!\XLite::isAdminZone()) {
            foreach ($items as $item) {
                if ($item->isDeleted()) {
                    $items->removeElement($item);
                    \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->delete($item);
                }
            }
        }

        return $items;
    }

    /**
     * Prepare order before remove operation
     *
     * @return void
     * @PreRemove
     */
    public function prepareBeforeRemove()
    {
        parent::prepareBeforeRemove();
    }

    /**
     * Mark cart as order
     *
     * @return void
     */
    public function markAsOrder()
    {
        parent::markAsOrder();

        if ($this instanceof \XLite\Model\Cart) {
            $this->assignOrderNumber();
        }

        $this->getRepository()->markAsOrder($this->getOrderId());
    }

    /**
     * Check if the cart has a "Cart" status. ("in progress", "temporary")
     *
     * @return boolean
     */
    public function hasCartStatus()
    {
        return $this instanceof \XLite\Model\Cart;
    }

    /**
     * If we can proceed with checkout with current cart
     *
     * @return boolean
     */
    public function checkCart()
    {
        return
            !$this->isEmpty()
            && !((bool) $this->getItemsWithWrongAmounts())
            && !$this->isMinOrderAmountError()
            && !$this->isMaxOrderAmountError()
            && $this->isConfigured();
    }

    /**
     * Login operation
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     */
    public function login(\XLite\Model\Profile $profile)
    {
        if ($this->isEmpty()) {
            if ($this->getProfile() && !$this->getProfile()->getAnonymous()) {
                $this->setProfile(null);
            }

            \XLite\Core\Database::getEM()->remove($this);

        } else {
            $this->mergeWithProfile($profile);

            $this->setProfile($profile);
            $this->setOrigProfile($profile);

            \XLite\Core\Session::getInstance()->unsetBatch(
                'same_address',
                'order_create_profile',
                'createProfilePassword',
                'lastLoginUnique'
            );
        }
    }

    /**
     * Returns the list of session vars that must be cleared on logoff
     *
     * @return array
     */
    public function getSessionVarsToClearOnLogoff()
    {
        return [
            'same_address',
            'order_id'
        ];
    }

    /**
     * Clear some session variables on logout
     *
     * @return void
     */
    protected function clearSessionVarsOnLogoff()
    {
        foreach ($this->getSessionVarsToClearOnLogoff() as $name) {
            unset(\XLite\Core\Session::getInstance()->$name);
        }
    }

    /**
     * Logoff operation
     *
     * @return void
     */
    public function logoff()
    {
        $this->clearSessionVarsOnLogoff();
    }

    /**
     * Initialize new cart
     *
     * @return void
     */
    protected function initializeCart()
    {
        $this->reinitializeCurrency();

        \XLite\Core\Session::getInstance()->unsetBatch(
            'same_address',
            'order_create_profile',
            'createProfilePassword'
        );
    }

    /**
     * Merge cart with with other carts specified profile
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     */
    public function mergeWithProfile(\XLite\Model\Profile $profile)
    {
        // Merge carts
        $carts = $this->getRepository()->findByProfile($profile);

        if ($carts) {
            foreach ($carts as $cart) {
                $this->merge($cart);
                $cart->setProfile(null);
                $cart->setOrigProfile(null);
                \XLite\Core\Database::getEM()->remove($cart);
            }
        }

        // Merge old addresses
        if ($this->getProfile()) {
            // Merge shipping address
            if ($this->getProfile()->getShippingAddress() && $this->getProfile()->getShippingAddress()->getIsWork()) {
                $address = $this->getProfile()->getShippingAddress();
                if ($profile->getShippingAddress()) {
                    if ($profile->getShippingAddress()->getIsWork()) {
                        \XLite\Core\Database::getEM()->remove($profile->getShippingAddress());
                        $profile->getAddresses()->removeElement($profile->getShippingAddress());

                    } else {
                        $profile->getShippingAddress()->setisShipping(false);
                    }
                }

                $address->setProfile($profile);
                $this->getProfile()->getAddresses()->removeElement($address);
                $profile->addAddresses($address);
            }

            // Merge billing address
            if ($this->getProfile()->getBillingAddress()
                && $this->getProfile()->getBillingAddress()->getIsWork()
            ) {
                $address = $this->getProfile()->getBillingAddress();
                if ($profile->getBillingAddress()) {
                    if ($profile->getBillingAddress()->getIsWork()
                        && !$profile->getBillingAddress()->getIsShipping()
                    ) {
                        \XLite\Core\Database::getEM()->remove($profile->getBillingAddress());
                        $profile->getAddresses()->removeElement($profile->getBillingAddress());

                    } else {
                        $profile->getBillingAddress()->setIsBilling(false);
                    }
                }

                $address->setProfile($profile);
                $this->getProfile()->getAddresses()->removeElement($address);
                $profile->addAddresses($address);
            }

        }

    }

    /**
     * Merge
     *
     * @param \XLite\Model\Cart $cart Cart
     *
     * @return \XLite\Model\Cart
     */
    public function merge(\XLite\Model\Cart $cart)
    {
        if (!$cart->isEmpty()) {
            foreach ($cart->getItems() as $item) {
                $cart->getItems()->removeElement($item);
                $item->setOrder($this);
                $this->addItems($item);
            }
        }

        $this->updateOrder();
    }
}
