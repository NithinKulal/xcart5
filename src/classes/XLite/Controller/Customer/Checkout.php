<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Checkout
 */
class Checkout extends \XLite\Controller\Customer\Cart
{
    /**
     * Checkout availability flag
     */
    const CHECKOUT_AVAIL_FLAG = 'checkout_avail_flag';

    /**
     * Checkout available flag timeout (24 hours)
     */
    const CHECKOUT_AVAIL_TIMEOUT = 86400;

    /**
     * Request data
     *
     * @var mixed
     */
    protected $requestData;

    /**
     * Payment widget data
     *
     * @var array
     */
    protected $paymentWidgetData = array();

    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $shippingModifier;

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return \XLite::getInstance()->getFormIdStrategy() === 'per-session';
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array(
                'return'
            )
        );
    }

    /**
     * Go to cart view if cart is empty
     *
     * @return void
     */
    public function handleRequest()
    {
        // We do not verify the cart when the customer returns from the payment
        if (\XLite\Core\Request::getInstance()->action !== 'return'
            && !$this->getCart()->checkCart()
        ) {
            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL('cart'));
            $this->doRedirect();
        }

        parent::handleRequest();
    }

    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        if (!\XLite\Core\Request::getInstance()->isBot()) {
            parent::callAction();
        }
    }

    /**
     * Get substep number
     *
     * @param string $name Substep name
     *
     * @return integer
     */
    public function getSubstepNumber($name)
    {
        $modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        $shippable = (!$modifier || !$modifier->canApply()) ? 0 : 1;

        switch ($name) {
            case 'shippingAddress':
                $result = 1;
                break;

            case 'billingAddress':
                $result = $shippable ? 2 : 1;
                break;

            case 'shippingMethods':
                $result = 3;
                break;

            case 'paymentMethods':
                $result = $shippable ? 4 : 2;
                break;

            default:
                $result = 1;
        }

        return $result;
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Checkout');
    }

    /**
     * External call processSucceed() method
     * TODO: to revise
     *
     * @return void
     */
    public function callSuccess()
    {
        $this->processSucceed();
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Get login URL
     *
     * @param string $email Email OPTIONAL
     *
     * @return string
     */
    public function getLoginURL($email = null)
    {
        $params = array();
        if ($email) {
            $params['login'] = $email;
        }

        return $this->buildURL('login', null, $params);
    }

    /**
     * Check - current profile is anonymous or not
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        $cart = $this->getCart();

        return !$cart->getProfile() || $cart->getProfile()->getAnonymous();
    }

    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function isAccountLinksVisible()
    {
        return parent::isAccountLinksVisible() && $this->isCheckoutAvailable();
    }

    /**
     * Define if the checkout is available
     * On the other hand the sign-in page is available only
     *
     * @return boolean
     */
    public function isCheckoutAvailable()
    {
        return \XLite\Core\Config::getInstance()->General->force_login_before_checkout
            ? $this->isLoggedOrAllowedAnonymous()
            : true;
    }

    public function isLoggedOrAllowedAnonymous()
    {
        return $this->isLogged()
            || (
                $this->getCart()->getProfile()
                && $this->getCart()->getProfile()->getAnonymous()
                && $this->getCheckoutAvailable()
            );
    }

    /**
     * Get payment widget data
     *
     * @return array
     */
    public function getPaymentWidgetData()
    {
        return $this->paymentWidgetData;
    }

    /**
     * Get modifier
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

    /**
     * Checkout
     * TODO: to revise
     *
     * @return void
     */
    protected function doActionCheckout()
    {
        $this->setHardRedirect();
        $cart = $this->getCart();

        $itemsBeforeUpdate = $cart->getItemsFingerprint();
        $this->updateCart();
        $itemsAfterUpdate = $cart->getItemsFingerprint();

        if ($itemsAfterUpdate !== $itemsBeforeUpdate
            || $this->get('absence_of_product')
            || $cart->isEmpty()
        ) {
            // Cart is changed
            $this->set('absence_of_product', true);
            $this->redirect($this->buildURL('cart'));

        } elseif (!$this->checkCheckoutAction()) {
            // Check access
            $this->redirect($this->buildURL('checkout'));

        } else {
            $data = is_array(\XLite\Core\Request::getInstance()->payment)
                ? \XLite\Core\Request::getInstance()->payment
                : array();

            $errors = array();
            $firstOpenTransaction = $cart->getFirstOpenPaymentTransaction();
            if ($firstOpenTransaction) {
                $errors = $firstOpenTransaction->getPaymentMethod()
                    ->getProcessor()
                    ->getInputErrors($data);
            }

            if ($errors) {
                foreach ($errors as $error) {
                    \XLite\Core\TopMessage::addError($error);
                }

                $this->redirect($this->buildURL('checkout'));

            } else {
                $shippingMethod = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findOneBy(
                    array(
                        'method_id' => $cart->getShippingId()
                    )
                );

                if ($shippingMethod) {
                    $cart->setShippingMethodName($shippingMethod->getName());
                } else {
                    $cart->setShippingMethodName(null);
                }

                // Register 'Place order' event in the order history
                \XLite\Core\OrderHistory::getInstance()->registerPlaceOrder($cart->getOrderId());

                // Register 'Order packaging' event in the order history
                \XLite\Core\OrderHistory::getInstance()->registerOrderPackaging(
                    $cart->getOrderId(),
                    $cart->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING')
                );

                $cart->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);

                // Make order payment step
                $this->doPayment();

                // Clear widget cache if any of the purchased products became out of stock (product list widgets may be re-rendered):
                /** @var \XLite\Model\OrderItem $item */
                foreach ($cart->getItems() as $item) {
                    if ($item->getInventoryAmount() <= 0) {
                        $this->getContainer()->get('widget_cache_manager')->deleteAll();

                        break;
                    }
                }
            }
        }
    }

    /**
     * Controller marks the cart calculation.
     * On the checkout page we need cart recalculation
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return true;
    }

    /**
     * Do payment
     * :TODO: to revise
     * :FIXME: decompose
     *
     * @return void
     */
    protected function doPayment()
    {
        $this->setHardRedirect();
        $cart = $this->getCart();

        if (isset(\XLite\Core\Request::getInstance()->notes)) {
            $cart->setNotes(\XLite\Core\Request::getInstance()->notes);
        }

        if ($cart->hasCartStatus()) {
            $cart->setDate(\XLite\Core\Converter::time());
        }

        // Get first (and only) payment transaction
        $transaction = $cart->getFirstOpenPaymentTransaction();
        $result = null;
        $paymentStatusCode = null;

        if ($transaction) {
            // Process transaction
            $result = $transaction->handleCheckoutAction();

            $pstatus = $cart->getCalculatedPaymentStatus(true);
            if ($pstatus !== \XLite\Model\Order\Status\Payment::STATUS_QUEUED) {
                $paymentStatusCode = $pstatus;
            }

        } elseif (!$cart->isOpen()) {
            // Cart is payed - create dump transaction
            $result = \XLite\Model\Payment\Transaction::COMPLETED;
            $paymentStatusCode = $cart->getCalculatedPaymentStatus(true);

        } else {
            $paymentStatusCode = $cart->getPaymentStatusCode();
        }

        if (\XLite\Model\Payment\Transaction::PROLONGATION === $result) {
            $this->set('silent', true);

            exit (0);

        } elseif (\XLite\Model\Payment\Transaction::SILENT === $result) {
            $this->paymentWidgetData = $transaction->getPaymentMethod()
                ->getProcessor()
                ->getPaymentWidgetData();
            $this->set('silent', true);

        } elseif (\XLite\Model\Payment\Transaction::SEPARATE === $result) {
            $this->setReturnURL($this->buildURL('checkoutPayment'));

        } elseif ($cart->isOpen()) {
            // Order is open - go to Select payment method step
            if ($transaction && $transaction->getNote()) {
                \XLite\Core\TopMessage::getInstance()->add(
                    $transaction->getNote(),
                    array(),
                    null,
                    $transaction->isFailed()
                        ? \XLite\Core\TopMessage::ERROR
                        : \XLite\Core\TopMessage::INFO,
                    true
                );
            }

            $this->setReturnURL($this->buildURL('checkout'));

        } else {
            if ($cart->isPayed()) {
                $paymentStatus = $paymentStatusCode ?: \XLite\Model\Order\Status\Payment::STATUS_PAID;
                $cart->setPaymentStatus($paymentStatus);
                $this->processSucceed();

            } elseif ($transaction && $transaction->isFailed()) {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;
                $cart->setPaymentStatus($paymentStatus);
                \XLite\Core\Database::getEM()->flush();

            } else {
                $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
                $cart->setPaymentStatus($paymentStatus);
                $this->processSucceed();
            }

            \XLite\Core\TopMessage::getInstance()->clearTopMessages();

            $this->setReturnURL(
                $this->buildURL(
                    \XLite\Model\Order\Status\Payment::STATUS_DECLINED === $paymentStatus
                        ? 'checkoutFailed'
                        : 'checkoutSuccess',
                    '',
                    $cart->getOrderNumber()
                        ? array('order_number' => $cart->getOrderNumber())
                        : array('order_id' => $cart->getOrderId())
                )
            );
        }

        // Commented out in connection with E:0041438
        //$this->updateCart();
    }

    /**
     * Return from payment gateway
     *
     * :TODO: to revise
     * :FIXME: decompose
     *
     * @return void
     */
    protected function doActionReturn()
    {
        // some of gateways can't accept return url on run-time and
        // use the one set in merchant account, so we can't pass
        // 'order_id' in run-time, instead pass the order id parameter name
        $orderId = \XLite\Core\Request::getInstance()->order_id;

        /** @var \XLite\Model\Order $cart */
        $cart = \XLite\Core\Database::getRepo('XLite\Model\Cart')->find($orderId)
            ?: \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($cart) {
            \XLite\Model\Cart::setObject($cart);
        }

        if (!$cart) {
            // Cart not found
            unset(\XLite\Core\Session::getInstance()->order_id);
            \XLite\Core\TopMessage::addError('Order not found');
            $this->setReturnURL($this->buildURL('cart'));

        } elseif ($cart->getOpenTotal() > 0) {
            // Order still not payed
            $this->assignTransactionMessage();
            $this->setReturnURL($this->buildURL('checkout'));

        } else {
            // Order payed or pending
            if ($cart instanceof \XLite\Model\Cart) {
                $cart->tryClose();
                \XLite\Core\Database::getEM()->flush();
            }

            \XLite\Core\Session::getInstance()->last_order_id = $orderId;

            \XLite\Core\TopMessage::getInstance()->clearTopMessages();
            $this->setReturnURL(
                $this->buildURL(
                    $this->getStatusTarget($cart->getPaymentStatusCode()),
                    '',
                    $cart->getOrderNumber()
                        ? array('order_number' => $cart->getOrderNumber())
                        : array('order_id' => $orderId)
                )
            );
        }
    }

    /**
     * Add top message about transaction results
     *
     * @return void
     */
    protected function assignTransactionMessage()
    {
        $txnNote = \XLite\Core\Request::getInstance()->txnNote
            ? base64_decode(\XLite\Core\Request::getInstance()->txnNote)
            : null;
        $txnNoteType = \XLite\Core\Request::getInstance()->txnNoteType;

        if ($txnNote) {
            $message = strip_tags($txnNote);

            if (\XLite\Core\TopMessage::ERROR === $txnNoteType) {
                if (\XLite\Model\Payment\Transaction::getDefaultFailedReason() == $txnNote) {
                    // Display default message
                    \XLite\Core\TopMessage::addError($message);

                } else {
                    // Display specific transaction message
                    \XLite\Core\TopMessage::addError($this->getCommonErrorMessage(), array('txnNote' => $message));
                }


            } else {
                // Display transaction success message
                \XLite\Core\TopMessage::addInfo($message);
            }
        }
    }

    /**
     * Defines the target to return according the order status
     *
     * @param string $paymentStatus Order status
     *
     * @return string Target name
     */
    protected function getStatusTarget($paymentStatus)
    {
        switch ($paymentStatus) {
            case \XLite\Model\Order\Status\Payment::STATUS_CANCELED:
                $result = 'checkoutCanceled';
                break;

            case \XLite\Model\Order\Status\Payment::STATUS_DECLINED:
                $result = 'checkoutFailed';
                break;

            default:
                $result = 'checkoutSuccess';
        }

        return $result;
    }

    /**
     * Order placement is success
     *
     * :TODO: to revise
     * :FIXME: decompose
     *
     * @param boolean $fullProcess Full process or not OPTIONAL
     *
     * @return void
     */
    public function processSucceed($fullProcess = true)
    {
        $cart = $this->getCart();

        if ($fullProcess) {
            $cart->processSucceed();
        }

        $this->processCartProfile($fullProcess);

        // Save order id in session and forget cart id from session
        \XLite\Core\Session::getInstance()->last_order_id = $cart->getOrderId();
        \XLite\Core\Session::getInstance()->unsetBatch(
            'order_id',
            'order_create_profile',
            'saveShippingAsNew',
            'createProfilePassword',
            'lastLoginUnique',
            static::CHECKOUT_AVAIL_FLAG
        );

        // Commented out in connection with E:0041438
        //$this->updateCart();

        // Mark all addresses as non-work
        if ($cart->getOrigProfile()) {
            foreach ($cart->getOrigProfile()->getAddresses() as $address) {
                $address->setIsWork(false);
            }
        }

        if ($cart->getProfile()) {
            foreach ($cart->getProfile()->getAddresses() as $address) {
                $address->setIsWork(false);
            }
        }

        \XLite\Core\Database::getEM()->flush();
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
        $isAnonymous = $this->isAnonymous();

        if ($isAnonymous) {
            if (\XLite\Core\Session::getInstance()->order_create_profile) {
                // Create profile based on anonymous order profile
                $this->saveAnonymousProfile();
                $this->loginAnonymousProfile();

                $this->getCart()->getOrigProfile()->setPassword(
                    \XLite\Core\Auth::encryptPassword(\XLite\Core\Session::getInstance()->createProfilePassword)
                );

                $isAnonymous = false;

            } elseif ($doCloneProfile) {
                $this->mergeAnonymousProfile();
            }

        } elseif ($doCloneProfile) {
            // Clone profile
            $this->cloneProfile();
            $isAnonymous = false;
        }

        return $isAnonymous;
    }

    /**
     * Save anonymous profile
     *
     * @return void
     */
    protected function saveAnonymousProfile()
    {
        $cart = $this->getCart();

        // Create cloned profile
        $profile = $cart->getProfile()->cloneEntity();

        // Generate password
        $profile->setAnonymous(false);
        $profile->setOrder(null);

        // Set cloned profile as original profile
        $cart->setOrigProfile($profile);

        // Send notifications
        $this->sendCreateProfileNotifications();
    }

    /**
     * Merge anonymous profile
     *
     * @return void
     */
    protected function mergeAnonymousProfile()
    {
        $cart = $this->getCart();
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->findOneAnonymousByProfile($cart->getProfile());

        if ($profile) {
            $profile->mergeWithProfile(
                $cart->getProfile(),
                \XLite\Model\Profile::MERGE_ALL ^ \XLite\Model\Profile::MERGE_ORDERS
            );

        } else {
            $profile = $cart->getProfile()->cloneEntity();
            $profile->setOrder(null);
            $profile->setAnonymous(true);
        }
        $cart->setOrigProfile($profile);

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Login anonymous profile
     *
     * @return void
     */
    protected function loginAnonymousProfile()
    {
        \XLite\Core\Auth::getInstance()->loginProfile($this->getCart()->getOrigProfile());
    }

    /**
     * Send create profile notifications
     *
     * @param string $password Password OPTIONAL
     *
     * @return void
     */
    protected function sendCreateProfileNotifications($password = null)
    {
        $profile = $this->getCart()->getOrigProfile();

        // Send notification
        \XLite\Core\Mailer::sendProfileCreated($profile, $password, true);
    }

    /**
     * Clone profile and move profile to original profile
     *
     * @return void
     */
    protected function cloneProfile()
    {
        $cart = $this->getCart();

        $origProfile = $cart->getProfile();
        $profile = $origProfile->cloneEntity();

        // Assign cloned order's profile
        $cart->setProfile($profile);
        $profile->setOrder($cart);

        // Save old profile as original profile
        $cart->setOrigProfile($origProfile);
        $origProfile->setOrder(null);

        if (\XLite\Core\Session::getInstance()->checkoutEmail
            && \XLite\Core\Session::getInstance()->checkoutEmail !== $cart->getProfile()->getLogin()
        ) {
            $cart->getProfile()->setLogin(\XLite\Core\Session::getInstance()->checkoutEmail);
        }
        unset(\XLite\Core\Session::getInstance()->checkoutEmail);

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * isRegistrationNeeded
     * (CHECKOUT_MODE_REGISTER step check)
     *
     * @return boolean
     */
    protected function isRegistrationNeeded()
    {
        return !\XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Check if order total is zero
     *
     * @return boolean
     */
    protected function isZeroOrderTotal()
    {
        return 0 == $this->getCart()->getTotal()
            && \XLite\Core\Config::getInstance()->Payments->default_offline_payment;
    }

    /**
     * Check - is order shippable or not
     *
     * @return boolean
     */
    public function isShippingNeeded()
    {
        return $this->getShippingModifier() && $this->getShippingModifier()->canApply();
    }

    /**
     * Check if we are ready to select payment method
     *
     * @return boolean
     */
    protected function isPaymentNeeded()
    {
        $cart = $this->getCart();

        return !$cart->getPaymentMethod() && $cart->getOpenTotal();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Check amount for all cart items
     *
     * @return void
     */
    protected function checkItemsAmount()
    {
        // Do not call parent: it's only needed to check amounts in cart, not on checkout
    }

    /**
     * The checkout availability flag
     *
     * @return boolean
     */
    protected function getCheckoutAvailable()
    {
        // Checkout available flag will be reset after 24 hours (86400 seconds)
        return ((int) \XLite\Core\Session::getInstance()->{static::CHECKOUT_AVAIL_FLAG}
            + static::CHECKOUT_AVAIL_TIMEOUT) >= time();
    }

    /**
     * The checkout is available now (Sign-in page is hidden now)
     *
     * @return void
     */
    protected function setCheckoutAvailable()
    {
        \XLite\Core\Session::getInstance()->{static::CHECKOUT_AVAIL_FLAG} = time();
    }

    /**
     * Update profile
     *
     * @return void
     */
    protected function doActionUpdateProfile()
    {
        $form = new \XLite\View\Form\Checkout\UpdateProfile();

        $this->requestData = $form->getRequestData();

        $this->updateProfile();

        $this->updateShippingAddress();

        $this->updateBillingAddress();

        $this->setCheckoutAvailable();

        if (empty($this->requestData['shippingAddress'])
            && empty($this->requestData['billingAddress'])
            && isset($this->requestData['same_address'])
        ) {
            \XLite\Core\Session::getInstance()->same_address = (bool)$this->requestData['same_address'];
        }

        $this->updateCart();

        if (\XLite\Core\Request::getInstance()->isGet() && !$this->getReturnURL()) {
            $this->setReturnURL($this->buildURL('checkout'));
        }
    }

    /**
     * Update profile
     *
     * @return void
     */
    protected function updateProfile()
    {
        if ($this->isAnonymous()) {
            $this->updateAnonymousProfile();

        } else {
            $this->updateLoggedProfile();
        }
    }

    /**
     * Update anonymous profile
     *
     * @return void
     */
    protected function updateAnonymousProfile()
    {
        $login = $this->requestData['email'];

        if (null !== $login) {
            $tmpProfile = new \XLite\Model\Profile;
            $tmpProfile->setProfileId(0);
            $tmpProfile->setLogin($login);

            $exists = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                ->checkRegisteredUserWithSameLogin($tmpProfile);

            if ($exists) {
                \XLite\Core\Session::getInstance()->order_create_profile = false;
            }

            \XLite\Core\Session::getInstance()->lastLoginUnique = !$exists;

            $profile = $this->getCartProfile();
            $profile->setLogin($login);
            \XLite\Core\Database::getEM()->flush($profile);

            \XLite\Core\Event::loginExists(
                array(
                    'value' => $exists,
                )
            );

            if ($exists && $this->requestData['create_profile']) {
                // Profile with same login is exists
                $this->valid = false;

            } elseif (isset($this->requestData['password'])
                && !$this->requestData['password']
                && $this->requestData['create_profile']
            ) {
                $this->valid = false;

                $label = static::t('Field is required!');

                \XLite\Core\Event::invalidElement('password', $label);

            } elseif (false !== $this->valid) {
                \XLite\Core\Session::getInstance()->order_create_profile = (bool)$this->requestData['create_profile'];

                if (\XLite\Core\Session::getInstance()->order_create_profile) {
                    \XLite\Core\Session::getInstance()->createProfilePassword = $this->requestData['password'];
                }
            }
        }
    }

    /**
     * Update logged profile
     *
     * @return void
     */
    protected function updateLoggedProfile()
    {
        $login = $this->requestData['email'];

        if (null !== $login) {
            \XLite\Core\Session::getInstance()->checkoutEmail = $login;
        }
    }

    /**
     * Update shipping address
     *
     * @return void
     */
    protected function updateShippingAddress()
    {
        $data = $this->requestData['shippingAddress'];

        $profile = $this->getCartProfile();

        $address = $this->prepareShippingAddress();

        if (is_array($data) || !$this->getAddressFields()) {
            $data = is_array($data) ? $data : array();

            $noAddress = null === $address;

            $andAsBilling = false;

            $new = new \XLite\Model\Address;
            $new->map($this->prepareAddressData($data, 'shipping'));
            $equal = null;
            foreach ($profile->getAddresses() as $profileAddress) {
                if ($profileAddress->isEqualAddress($new)
                    && (!$address || $address->getAddressId() != $profileAddress->getAddressId())
                ) {
                    $equal = $profileAddress;
                    break;
                }
            }

            if ($equal) {
                $profile->setShippingAddress($equal);
                $noAddress = false;
            }

            if ($noAddress || (!$address->getIsWork() && !$address->isEqualAddress($new))) {

                if (!$noAddress) {
                    $andAsBilling = $address->getIsBilling();
                    $address->setIsBilling(false);
                    $address->setIsShipping(false);
                }

                $address = new \XLite\Model\Address;

                $address->setProfile($profile);
                $address->setIsShipping(true);
                $address->setIsBilling($andAsBilling);
                $address->setIsWork(true);

                if ($noAddress || !(bool)\XLite\Core\Request::getInstance()->only_calculate) {
                    $profile->addAddresses($address);
                    \XLite\Core\Database::getEM()->persist($address);
                }
            }

            $address->map($this->prepareAddressData($data, 'shipping'));

            if ($noAddress
                && (\XLite\Core\Session::getInstance()->same_address
                    || null === \XLite\Core\Session::getInstance()->same_address
                )
                && !$profile->getBillingAddress()
            ) {
                // Same address as default behavior
                $address->setIsBilling(true);
            }

            \XLite\Core\Session::getInstance()->same_address = $this->getCart()->getProfile()->isEqualAddress();

            if ($noAddress) {
                \XLite\Core\Event::createShippingAddress(array('id' => $address->getAddressId()));
            }
        }
    }

    /**
     * Update profile billing address
     *
     * @return void
     */
    protected function updateBillingAddress()
    {
        $noAddress = false;

        $data = empty($this->requestData['billingAddress']) ? null : $this->requestData['billingAddress'];

        $profile = $this->getCartProfile();

        if (isset($this->requestData['same_address'])) {
            \XLite\Core\Session::getInstance()->same_address = (bool)$this->requestData['same_address'];
        }

        if ($this->requestData['same_address'] && !$profile->isEqualAddress()) {
            // Shipping and billing are same addresses
            $address = $profile->getBillingAddress();

            if ($address) {
                // Unselect old billing address
                $address->setIsBilling(false);

                if ($address->getIsWork()) {
                    $profile->getAddresses()->removeElement($address);
                    \XLite\Core\Database::getEM()->remove($address);
                }
            }

            $address = $profile->getShippingAddress();

            if ($address) {
                // Link shipping and billing address
                $address->setIsBilling(true);
            }

        } elseif (isset($this->requestData['same_address'])
            && !$this->requestData['same_address'] && $profile->isEqualAddress()
        ) {
            $this->unlinkShippingFromBilling();
        }

        if (is_array($data) && !$this->requestData['same_address']) {
            // Save separate billing address
            $address = $profile->getBillingAddress();

            if ($address && $address->isPersistent()) {
                \XLite\Core\Database::getEM()->refresh($address);
            }

            $andAsShipping = false;

            $new = new \XLite\Model\Address;
            $new->map($this->prepareAddressData($data, 'billing'));
            $equal = null;

            foreach ($profile->getAddresses() as $addressEqual) {
                if ($addressEqual->isEqualAddress($new)
                    && (!$address || $address->getAddressId() != $addressEqual->getAddressId())
                ) {
                    $equal = $addressEqual;
                    break;
                }
            }

            if ($equal) {
                $profile->setBillingAddress($equal);
            }

            if (!$address || (!$address->getIsWork() && !$address->isEqualAddress($new))) {
                if ($address) {
                    $andAsShipping = $address->getIsShipping();
                    $address->setIsBilling(false);
                    $address->setIsShipping(false);
                }

                $address = new \XLite\Model\Address;
                $address->setProfile($profile);
                $address->setIsBilling(true);
                $address->setIsShipping($andAsShipping);
                $address->setIsWork(true);

                if (!(bool)\XLite\Core\Request::getInstance()->only_calculate) {
                    $profile->addAddresses($address);

                    \XLite\Core\Database::getEM()->persist($address);
                    $noAddress = true;
                }
            }

            $address->map($this->prepareAddressData($data, 'billing'));

            \XLite\Core\Session::getInstance()->same_address = $this->getCart()->getProfile()->isEqualAddress();
        }

        if ($noAddress) {
            \XLite\Core\Event::createBillingAddress(array('id' => $address->getAddressId()));
        }
    }

    /**
     * Prepares shipping address to update
     * 
     * @return \XLite\Model\Address
     */
    protected function prepareShippingAddress()
    {
        $profile = $this->getCartProfile();

        $address = $profile->getShippingAddress();

        if ($address && $address->isPersistent()) {
            \XLite\Core\Database::getEM()->refresh($address);
        }

        return $address;
    }

    /**
     * Separate shipping and billing addresses
     */
    protected function unlinkShippingFromBilling()
    {
        $profile = $this->getCartProfile();
        $address = $profile->getShippingAddress();

        if ($address && $address->getIsBilling()) {
            $address->setIsBilling(false);
        }
    }

    /**
     * Prepare address data
     *
     * @param array  $data Address data
     * @param string $type Address type OPTIONAL
     *
     * @return array
     */
    protected function prepareAddressData(array $data, $type = 'shipping')
    {
        unset($data['save_in_book']);

        $requiredFields = 'shipping' === $type
            ? \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getShippingRequiredFields()
            : \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getBillingRequiredFields();

        foreach ($requiredFields as $fieldName) {
            if (!isset($data[$fieldName]) && \XLite\Model\Address::getDefaultFieldValue($fieldName)) {
                $data[$fieldName] = \XLite\Model\Address::getDefaultFieldValue($fieldName);
            }
        }

        return $data;
    }

    /**
     * Set payment method
     *
     * @return void
     */
    protected function doActionPayment()
    {
        /** @var \XLite\Model\Payment\Method $pm */
        $pm = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->find(\XLite\Core\Request::getInstance()->methodId);

        if (!$pm) {
            \XLite\Core\TopMessage::addError(
                'No payment method selected'
            );

        } else {
            $cart = $this->getCart();
            $profile = $cart->getProfile();
            if (null !== $profile) {
                $profile->setLastPaymentId($pm->getMethodId());
            }

            $cart->setPaymentMethod($pm);

            $this->updateCart();

            if ($this->isPaymentNeeded()) {
                \XLite\Core\TopMessage::addError(
                    'The selected payment method is obsolete or invalid. Select another payment method'
                );
            }
        }
    }

    /**
     * Change shipping method
     *
     * @return void
     */
    protected function doActionShipping()
    {
        $methodId = \XLite\Core\Request::getInstance()->methodId;

        if (null !== $methodId) {
            $cart = $this->getCart();
            $cart->setLastShippingId($methodId);
            $cart->setShippingId($methodId);
            $this->updateCart();

        } else {
            $this->valid = false;
        }
    }

    /**
     * Check checkout action accessibility
     *
     * @return boolean
     */
    public function checkCheckoutAction()
    {
        $result = true;

        $steps = new \XLite\View\Checkout\Steps();
        foreach (array_slice($steps->getSteps(), 0, -1) as $step) {
            if (!$step->isCompleted()) {
                $result = false;
                break;
            }
        }

        return $result && $this->checkReviewStep();
    }

    /**
     * Check review step - complete or not
     *
     * @return string
     */
    protected function checkReviewStep()
    {
        return $this->getCart()->getProfile()->getLogin();
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        return array();
    }

    /**
     * Check - is service controller or not
     *
     * @return boolean
     */
    protected function isServiceController()
    {
        return 'return' === $this->getAction();
    }

    /**
     * Get common error message
     *
     * @return string
     */
    protected function getCommonErrorMessage()
    {
        return 'An error occurred, please try again. If the problem persists, contact the administrator. (txnNote)';
    }
}
