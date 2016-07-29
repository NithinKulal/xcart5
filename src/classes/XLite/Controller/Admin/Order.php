<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Order page controller
 */
class Order extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'order_id', 'order_number', 'page');

    /**
     * Order (local cache)
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Modifiers
     *
     * @var array
     */
    protected $modifiers;

    /**
     * Order changes
     *
     * @var array
     */
    protected static $changes = array();

    /**
     * Temporary orders list
     *
     * @var \XLite\Model\Order[]
     */
    protected static $tmpOrders;

    /**
     * Run-time flag: true if order items changes should affect stock
     *
     * @var boolean
     */
    protected static $isNeedProcessStock = false;

    /**
     * Cache of orders (for print invoice page)
     *
     * @var \XLite\Model\Order[]
     */
    protected $orders;


    /**
     * Return variable $isNeedProcessStock
     *
     * @return boolean
     */
    public static function isNeedProcessStock()
    {
        return static::$isNeedProcessStock;
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
            array('calculate_price', 'recalculate_shipping')
        );
    }

    // {{{ Temporary order related methods

    /**
     * Get temporary order (clone) for order with specified order ID
     *
     * @param integer $orderId Current order ID
     * @param boolean $force   Force temporary order creation if it doesn't exist OPTIONAL
     *
     * @return \XLite\Model\Order
     */
    public static function getTemporaryOrder($orderId, $force = true)
    {
        $result = null;

        $orderId = (int) $orderId;

        if (0 < $orderId) {
            $result = static::getTemporaryOrderFromCache($orderId)
                ?: ($force ? static::createTemporaryOrder($orderId) : null);
        }

        return $result;
    }

    /**
     * Get temporary order from cache
     *
     * @param integer $orderId Order ID
     *
     * @return \XLite\Model\Order
     */
    public static function getTemporaryOrderData($orderId = null)
    {
        $result = null;

        if (null === static::$tmpOrders) {
            // Initialize $tmpOrders
            static::$tmpOrders = array();
        }

        if ($orderId && isset(static::$tmpOrders[$orderId])) {
            // Get specific temporary order data from cache
            $result = static::$tmpOrders[$orderId];

        } else {
            $result = static::$tmpOrders;
        }

        return $result;
    }

    /**
     * Get temporary order from cache
     *
     * @param integer $orderId Order ID
     *
     * @return \XLite\Model\Order
     */
    protected static function getTemporaryOrderFromCache($orderId)
    {
        $result = null;

        if (null === static::$tmpOrders) {
            // Initialize $tmpOrders
            static::$tmpOrders = array();
        }

        if (isset(static::$tmpOrders[$orderId])) {
            // Get order from cache

            if (!(static::$tmpOrders[$orderId]['order'] instanceof \XLite\Model\Order)) {
                static::$tmpOrders[$orderId]['order'] = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->find((int) static::$tmpOrders[$orderId]['order']);
            }

            $result = static::$tmpOrders[$orderId]['order'];
        }

        return $result;
    }

    /**
     * Create temporary order
     *
     * @param integer $orderId Order ID
     *
     * @return \XLite\Model\Order
     */
    protected static function createTemporaryOrder($orderId)
    {
        $result = null;

        // Get current order by orderId
        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId);

        if ($order) {
            // Get temporary order as a clone
            $newOrder = $order->cloneEntity();

            if ($newOrder) {
                $newOrder->setOrderNumber(null);
                $newOrder->setIsNotificationsAllowedFlag(false);

                // Save cloned order in the database
                \XLite\Core\Database::getEM()->persist($newOrder);
                \XLite\Core\Database::getEM()->flush();

                // Transform temporary order to the cart object
                $newOrder->markAsCart();

                // Result is a cloned order
                $result = $newOrder;
                static::$tmpOrders[$orderId]['order'] = $newOrder;

                // Prepare the correspondence table of order items

                $itemIds = array();

                foreach ($newOrder->getItems() as $i) {
                    $itemIds[$i->getKey()] = $i->getItemId();
                }

                foreach ($order->getItems() as $item) {
                    $key = $item->getKey();
                    if (!empty($itemIds[$key])) {
                        static::$tmpOrders[$orderId]['items'][$item->getItemId()] = $itemIds[$key];
                    }
                }

            } else {
                \XLite\Core\TopMessage::addError('Cannot create temporary order for modification');
            }
        }

        return $result;
    }

    // }}}

    // {{{ Order changes processing

    /**
     * Set order changes
     *
     * @param string $name     Order property name
     * @param mixed  $newValue New property value
     * @param mixed  $oldValue Old property value
     *
     * @return void
     */
    public static function setOrderChanges($name, $newValue, $oldValue = null)
    {
        static::$changes[$name] = array(
            'old' => $oldValue,
            'new' => $newValue,
        );
    }

    /**
     * Return requested changes for the order
     *
     * @return array
     */
    protected function getOrderChanges()
    {
        $changes = array();

        foreach (static::$changes as $key => $data) {
            $names = explode(':', $key, 2);

            $name = static::getFieldHumanReadableName($names[0]);
            $subname = isset($names[1]) ? $names[1] : null;

            if ($subname) {
                $subname = static::getFieldHumanReadableName($subname);
                $changes[$name][$subname] = $data;

            } else {
                $changes[$name] = $data;
            }
        }

        return $changes;
    }

    /**
     * Get human readable field name
     *
     * @param string $name Field service name
     *
     * @return string
     */
    protected static function getFieldHumanReadableName($name)
    {
        $names = static::getFieldHumanReadableNames();

        return isset($names[$name]) ? $names[$name]: $name;
    }

    /**
     * Get human readable field names
     *
     * @return array
     */
    protected static function getFieldHumanReadableNames()
    {
        return array(
            'billingAddress'  => 'Billing address',
            'shippingAddress' => 'Shipping address',
            'shippingId'      => 'Shipping method',
            'paymentMethod'   => 'Payment method',
            'adminNote'       => 'Staff note',
            'customerNote'    => 'Customer note',
            'SHIPPING'        => 'Shipping cost',
            'firstname'       => 'First name',
            'lastname'        => 'Last name',
            'street'          => 'Address',
            'city'            => 'City',
            'zipcode'         => 'Zip code',
            'phone'           => 'Phone',
        );
    }

    // }}}

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        $request = \XLite\Core\Request::getInstance();

        if ($request->action
            && 'update' !== $request->action
        ) {
            $order = $this->getOrder();

            if (null !== $order) {
                $allowedTransactions = $order->getAllowedPaymentActions();

                if (isset($allowedTransactions[$request->action])) {
                    $request->transactionType = $request->action;
                    $request->action = 'PaymentTransaction';
                    $request->setRequestMethod('POST');
                }
            }
        }

        // Set ignoreLongCalculations mode for shipping rates gathering
        foreach (\XLite\Model\Shipping::getProcessors() as $processor) {
            if (!($processor instanceof \XLite\Model\Shipping\Processor\Offline)
                && $processor->isConfigured()
            ) {
                \XLite\Model\Shipping::setIgnoreLongCalculationsMode(true);

                break;
            }
        }

        parent::handleRequest();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        $orders = $this->getOrders();

        return parent::checkAccess()
            && $orders
            && $orders[0]
            && $orders[0]->getProfile();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ('invoice' === \XLite\Core\Request::getInstance()->mode) {
            $result = (
                \XLite\Core\Config::getInstance()->Company->company_name
                ? \XLite\Core\Config::getInstance()->Company->company_name . ': '
                : ''
            ) . static::t('Invoice');

        } elseif ('XLite\View\Address\OrderModify' === ltrim(\XLite\Core\Request::getInstance()->widget, '\\')) {
            $result = static::t('Customer information');

        } elseif ('XLite\View\SelectAddressOrder' === ltrim(\XLite\Core\Request::getInstance()->widget, '\\')) {
            $result = static::t('Pick address from address book');

        } elseif ('XLite\View\PaymentMethodData' === ltrim(\XLite\Core\Request::getInstance()->widget, '\\')) {
            $paymentMethod = null;
            if (intval(\XLite\Core\Request::getInstance()->transaction_id)) {
                $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                    ->find(\XLite\Core\Request::getInstance()->transaction_id);
                if ($transaction) {
                    $paymentMethod = $transaction->getPaymentMethod()
                        ? $transaction->getPaymentMethod()->getName()
                        : $transaction->getMethodName();
                }
            }
            $result = $paymentMethod ?: static::t('Payment method data');

        } else {
            $result = static::t('Order details');
        }

        return $result;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if (null === $this->order) {
            $order = null;
            if (\XLite\Core\Request::getInstance()->order_id) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->find((int) \XLite\Core\Request::getInstance()->order_id);

            } elseif (\XLite\Core\Request::getInstance()->order_number) {
                $order = \XLite\Core\Database::getRepo('XLite\Model\Order')
                    ->findOneByOrderNumber(\XLite\Core\Request::getInstance()->order_number);
            }

            $this->order = $order instanceof \XLite\Model\Cart
                ? null
                : $order;
        }

        return $this->order;
    }

    /**
     * Get list of orders (to print invoices)
     *
     * @return array
     */
    public function getOrders()
    {
        if (null === $this->orders) {
            $result = array();

            if (\XLite\Core\Request::getInstance()->order_ids) {
                $orderIds = explode(',', \XLite\Core\Request::getInstance()->order_ids);

                foreach ($orderIds as $orderId) {
                    $orderId = trim($orderId);
                    $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find((int) $orderId);
                    if ($order) {
                        $result[] = $order;
                    }
                }
            } else {
                $result[] = $this->getOrder();
            }

            $this->orders = $result;
        }

        return $this->orders;
    }

    /**
     * Return trus if page break is required after invoice page printing
     *
     * @param integer $index Index of order in the array of orders
     *
     * @return boolean
     */
    public function hasPageBreak($index)
    {
        return $index + 1 < count($this->orders);
    }

    /**
     *
     * @return boolean
     */
    public function isAdminNoteVisible()
    {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function isBillingAddressVisible()
    {
        return $this->getOrder()->getProfile()
            && $this->getOrder()->getProfile()->getBillingAddress();
    }

    /**
     *
     * @return boolean
     */
    public function isShippingAddressVisible()
    {
        return $this->getOrder()->getProfile()
            && $this->getOrder()->getProfile()->getShippingAddress();
    }

    /**
     *
     * @return boolean
     */
    public function isOrderItemsVisible()
    {
        return true;
    }

    /**
     * Check - item price is controlled by server or not
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    public function isPriceControlledServer(\XLite\Model\OrderItem $item)
    {
        return $item->isPriceControlledServer();
    }

    /**
     * Get address
     *
     * @return \XLite\Model\Address
     */
    public function getAddress()
    {
        $id = \XLite\Core\Request::getInstance()->addressId;

        return $id ? \XLite\Core\Database::getRepo('\XLite\Model\Address')->find($id) : null;
    }

    /**
     * Check - surcharge is controlled automatically or not
     *
     * @param array $surcharge Surcharge
     *
     * @return boolean
     */
    public function isAutoSurcharge(array $surcharge)
    {
        $data = \XLite\Core\Request::getInstance()->auto;

        return !empty($data)
            && !empty($data['surcharges'])
            && !empty($data['surcharges'][$surcharge['code']])
            && !empty($data['surcharges'][$surcharge['code']]['value']);
    }

    /**
     * Return true if order can be edited
     *
     * @return boolean
     */
    public function isOrderEditable()
    {
        return !\XLite::isFreeLicense();
    }

    /**
     * getRequestData
     * TODO: to remove
     *
     * @return array
     */
    protected function getRequestData()
    {
        return \Includes\Utils\ArrayManager::filterByKeys(
            \XLite\Core\Request::getInstance()->getData(),
            array('paymentStatus', 'shippingStatus')
        );
    }

    /**
     * Recalculate shipping rates of the source order
     *
     * @return void
     */
    protected function doActionRecalculateShipping()
    {
        if ($this->isOrderEditable()) {
            // Set ignoreLongCalculations mode for shipping rates gathering
            \XLite\Model\Shipping::setIgnoreLongCalculationsMode(false);

            // Get source order
            $order = $this->getOrder();

            if (\XLite\Core\Request::getInstance()->isAJAX()) {
                $this->displayRecalculateShippingData($order);
                $this->restoreFormId();
            }
        }
    }

    /**
     * Display recalculate shipping data
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    protected function displayRecalculateShippingData(\XLite\Model\Order $order)
    {
        \XLite\Core\Event::recalculateShipping($this->assembleRecalculateShippingEvent($order));
    }

    /**
     * Assemble recalculate shipping event
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function assembleRecalculateShippingEvent(\XLite\Model\Order $order)
    {
        $result = array(
            'options' => array(),
        );

        $modifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        $modifier->setMode(\XLite\Logic\Order\Modifier\AModifier::MODE_CART);

        foreach ($modifier->getRates() as $rate) {
            $result['options'][$rate->getMethod()->getMethodId()] = array(
                'name'     => html_entity_decode(
                    strip_tags($rate->getMethod()->getName()),
                    ENT_COMPAT,
                    'UTF-8'
                ),
                'fullName' => html_entity_decode(
                    $rate->getMethod()->getName(),
                    ENT_COMPAT,
                    'UTF-8'
                ),
            );
        }

        return $result;
    }

    /**
     * Recalculate order
     *
     * @return void
     */
    protected function doActionRecalculate()
    {
        if ($this->isOrderEditable()) {
            // Set ignoreLongCalculations mode for shipping rates gathering
            \XLite\Model\Shipping::setIgnoreLongCalculationsMode(false);

            // Initialize temprorary order
            $order = static::getTemporaryOrder($this->getOrder()->getOrderId(), true);

            // Update order items list
            $this->updateOrderItems($order);

            // Perform 'recalculate' action via model form
            $this->getOrderForm()->performAction('recalculate');

            if (\XLite\Core\Request::getInstance()->isAJAX()) {
                $this->displayRecalculateData($order);
                $this->displayRecalculateShippingData($order);
                $this->restoreFormId();
                $this->removeTemporaryOrder($order);
            }
        }
    }

    /**
     * Update order items list
     *
     * @param \XLite\Model\Order $order Order object
     *
     * @return void
     */
    protected function updateOrderItems($order)
    {
        $list = new \XLite\View\ItemsList\Model\OrderItem(
            array(
                'order' => $order,
            )
        );

        $list->processQuick();

        $order->calculateInitialValues();
    }

    /**
     * Display recalculate data
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    protected function displayRecalculateData(\XLite\Model\Order $order)
    {
        if ($this->needSendRecalculateEvent($order)) {
            \XLite\Core\Event::recalculateOrder($this->assembleRecalculateOrderEvent($order));
        }
    }

    /**
     * Remove temporary order
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    protected function removeTemporaryOrder(\XLite\Model\Order $order)
    {
        $origOrderId = null;

        // Search for index of data in temporary orders static cache
        if (is_array(static::$tmpOrders)) {
            foreach (static::$tmpOrders as $id => $data) {
                if ($id == $order->getOrderId()) {
                    $origOrderId = $id;
                    break;
                }
            }
        }

        \XLite\Core\Database::getEM()->remove($order->getProfile());

        // Remove temporary order
        \XLite\Core\Database::getEM()->remove($order);
        // \XLite\Core\Database::getEM()->flush();

        // Unset data in static cache
        if ($origOrderId) {
            unset(static::$tmpOrders[$origOrderId]);
        }
    }

    /**
     * Check - need send recalculate event or not
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function needSendRecalculateEvent(\XLite\Model\Order $order)
    {
        return true;
    }

    /**
     * Assemble recalculate order event
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function assembleRecalculateOrderEvent(\XLite\Model\Order $order)
    {
        $result =  array(
            'subtotal'  => $order->getSubtotal(),
            'total'     => $order->getTotal(),
            'modifiers' => array(),
        );

        foreach ($this->getSurchargeTotals(true) as $surcharge) {
            $result['modifiers'][$surcharge['code']] = abs($surcharge['cost']);
        }

        if ($this->isForbiddenOrderChanges($order)) {
            $result['forbidden'] = true;
        }

        return $result;
    }

    /**
     * Return true if order changes can not be saved
     *
     * @param \XLite\Model\Order $order Order entity
     *
     * @return boolean
     */
    protected function isForbiddenOrderChanges(\XLite\Model\Order $order)
    {
        $result = false;

        if (0 > $order->getTotal()) {
            $result = true;
            \XLite\Core\TopMessage::addError('Order changes cannot be saved due to negative total value');
        }

        return $result;
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $order = $this->getOrder();

        static::$isNeedProcessStock = true;

        // Set this flag to prevent duplicate 'Order changed' email notifications
        $this->getOrder()->setIgnoreCustomerNotifications(!$this->getSendNotificationFlag());
        $oldSentFlagValue = $this->getOrder()->setIsNotificationSent(true);

        if ($this->isOrderEditable()) {
            // Update order items list
            $this->updateOrderItems($order);

            // Update common order form fields
            $this->updateCommonForm();
        }

        $this->getOrder()->setIsNotificationSent($oldSentFlagValue);

        $this->updatePaymentMethods();

        // Process change order statuses
        $this->updateOrderStatus();

        // Process order tracking
        $this->updateTracking();

        // Update staff note
        $this->updateAdminNotes();

        // Update customer note (visible on invoice)
        $this->updateCustomerNotes();

        if ($this->getOrderChanges()) {
            // Register order changes
            \XLite\Core\OrderHistory::getInstance()
                ->registerGlobalOrderChanges($order->getOrderId(), $this->getOrderChanges());

            // Send notification
            $this->sendOrderChangeNotification();
        }
    }

    /**
     * Update common form
     *
     * @return void
     */
    protected function updateCommonForm()
    {
        $this->getOrderForm()->performAction('save');
    }

    /**
     * Update order status
     *
     * @return void
     */
    protected function updateOrderStatus()
    {
        $data = $this->getRequestData();
        $order = $this->getOrder();

        $updateRecent = false;
        foreach (array('paymentStatus', 'shippingStatus') as $status) {
            $method = 'get' . \XLite\Core\Converter::convertToCamelCase($status);
            // Call assembled $method: getPaymentStatus() or getShippingStatus()
            $oldStatus = $order->$method()->getId();
            if (!empty($data[$status]) && $oldStatus != $data[$status]) {
                $updateRecent = true;
            }
        }

        if ($updateRecent) {
            $data['recent'] = 0;
        }

        \XLite\Core\Database::getRepo('\XLite\Model\Order')->updateById(
            $order->getOrderId(),
            $data
        );
    }

    /**
     * Update tracking
     *
     * @return void
     */
    protected function updateTracking()
    {
        $list = new \XLite\View\ItemsList\Model\OrderTrackingNumber(
            array(
                \XLite\View\ItemsList\Model\OrderTrackingNumber::PARAM_ORDER_ID => $this->getOrder()->getOrderId(),
            )
        );
        $list->processQuick();
    }

    /**
     * Send order changed notification
     *
     * @return void
     */
    protected function sendOrderChangeNotification()
    {
        if ($this->getSendNotificationFlag() && !$this->getOrder()->isNotificationSent()) {
            \XLite\Core\Mailer::getInstance()->sendOrderAdvancedChangedCustomer($this->getOrder());
        }
    }

    /**
     * Get 'sendNotification' flag from request
     *
     * @return boolean
     */
    protected function getSendNotificationFlag()
    {
        return (bool) \XLite\Core\Request::getInstance()->sendNotification;
    }

    /**
     * Update staff note
     *
     * @return void
     */
    protected function updateAdminNotes()
    {
        $notes = \XLite\Core\Request::getInstance()->adminNotes;
        if (is_array($notes)) {
            $notes = reset($notes);
        }

        if (!$notes) {
            $notes = '';
        }

        $oldNotes = $this->getOrder()->getAdminNotes();

        if ($oldNotes != $notes) {

            $changes = array(
                'old' => $oldNotes,
                'new' => $notes,
            );

            \XLite\Core\OrderHistory::getInstance()
                ->registerOrderChangeAdminNotes($this->getOrder()->getOrderId(), $changes);

            $this->getOrder()->setAdminNotes($notes);

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Update customer note
     *
     * @return void
     */
    protected function updateCustomerNotes()
    {
        $notes = \XLite\Core\Request::getInstance()->notes;
        if (is_array($notes)) {
            $notes = reset($notes);
        }

        if (!$notes) {
            $notes = '';
        }

        $oldNotes = $this->getOrder()->getNotes();

        if ($oldNotes != $notes) {

            $changes = array(
                'old' => $this->getOrder()->getNotes(),
                'new' => $notes,
            );

            \XLite\Core\OrderHistory::getInstance()
                ->registerOrderChangeCustomerNotes($this->getOrder()->getOrderId(), $changes);

            $this->getOrder()->setNotes($notes);

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Update payment methods
     *
     * @return void
     */
    protected function updatePaymentMethods()
    {
        if ($this->isOrderEditable()) {
            $methods = \XLite\Core\Request::getInstance()->paymentMethods ?: array();

            foreach ($methods as $transactionId => $methodId) {
                $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find($transactionId);
                $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($methodId);
                $oldMethod = $transaction->getPaymentMethod();
                if ($method && $transaction && (!$oldMethod || $methodId != $oldMethod->getMethodId())) {
                    $transaction->setPaymentMethod($method);
                    \XLite\Core\OrderHistory::getInstance()
                        ->registerGlobalOrderChanges($this->getOrder()->getOrderId(), [
                            $this->getFieldHumanReadableName('paymentMethod') => [
                                'old'   => $oldMethod ? $oldMethod->getName() : null,
                                'new'   => $method->getName()
                            ]
                        ]);
                }
            }

            if ($method = $this->getOrder()->getPaymentMethod()) {
                $this->getOrder()->setPaymentMethod($method);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Send tracking information action
     *
     * @return void
     */
    protected function doActionSendTracking()
    {
        \XLite\Core\Mailer::sendOrderTrackingInformationCustomer($this->getOrder());

        \XLite\Core\TopMessage::addInfo('Tracking information has been sent');
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionPaymentTransaction()
    {
        $order = $this->getOrder();

        if ($order) {
            $transactions = $order->getPaymentTransactions();
            if (!empty($transactions)) {
                foreach ($transactions as $transaction) {
                    if ($transaction->getTransactionId() == \XLite\Core\Request::getInstance()->trn_id) {
                        $transaction->getPaymentMethod()->getProcessor()->doTransaction(
                            $transaction,
                            \XLite\Core\Request::getInstance()->transactionType
                        );
                    }
                }
            }
        }
    }

    /**
     * Calculate item price
     *
     * @return void
     */
    protected function doActionCalculatePrice()
    {
        if ($this->isOrderEditable()) {
            $request = \XLite\Core\Request::getInstance();

            $item = null;

            if ($request->item_id) {
                list($item, $attributes) = $this->getPreparedItemByItemId();
                if (!$item) {
                    $this->valid = false;
                }

            } elseif ($request->product_id) {
                list($item, $attributes) = $this->getPreparedItemByProductId();
            }

            if ($item) {
                $this->prepareItemBeforePriceCalculation($item, $attributes);
            }

            if (!$item) {
                $this->valid = false;

            } elseif (\XLite\Core\Request::getInstance()->isAJAX()) {
                $this->displayRecalculateItemPrice($item);
            }

            if (\XLite\Core\Request::getInstance()->isAJAX()) {
                $this->restoreFormId();
            }
        }
    }

    /**
     * Get prepared order item by item ID
     *
     * @return array
     */
    protected function getPreparedItemByItemId()
    {
        $order = $this->getOrder();
        $request = \XLite\Core\Request::getInstance();
        $attributeValues = array();
        $item = $order->getItemByItemId($request->item_id);

        if ($item
            && !empty($request->order_items[$request->item_id])
            && !empty($request->order_items[$request->item_id]['attribute_values'])
        ) {
            $attributeValues = $request->order_items[$request->item_id]['attribute_values'];
        }

        return array($item, $attributeValues);
    }

    /**
     * Get prepared order item by product ID
     *
     * @return array
     */
    protected function getPreparedItemByProductId()
    {
        $order = $this->getOrder();
        $request = \XLite\Core\Request::getInstance();
        $item = new \XLite\Model\OrderItem;
        $item->setOrder($order);
        $item->setProduct(\XLite\Core\Database::getRepo('XLite\Model\Product')->find($request->product_id));

        $attributes = $request->new;
        $attributes = reset($attributes);
        $attributeValues = array();
        if (!empty($attributes['attribute_values'])) {
            $attributeValues = $attributes['attribute_values'];
        }

        return array($item, $attributeValues);
    }

    /**
     * Prepare order item before price calculation
     *
     * @param \XLite\Model\OrderItem $item       Order item
     * @param array                  $attributes Attributes
     *
     * @return void
     */
    protected function prepareItemBeforePriceCalculation(\XLite\Model\OrderItem $item, array $attributes)
    {
        \XLite\Core\Request::getInstance()->oldAmount = $item->getAmount();

        $item->setAmount(\XLite\Core\Request::getInstance()->amount);

        if ($attributes) {
            $attributeValues = $item->getProduct()->prepareAttributeValues($attributes);
            $item->setAttributeValues($attributeValues);

            \XLite\Core\Database::getEM()->persist($item);
        }
    }

    /**
     * Display recalculate item price
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return void
     */
    protected function displayRecalculateItemPrice(\XLite\Model\OrderItem $item)
    {
        \XLite\Core\Event::recalculateItem($this->assembleRecalculateItemEvent($item));
    }

    /**
     * Assemble recalculate item event
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return array
     */
    protected function assembleRecalculateItemEvent(\XLite\Model\OrderItem $item)
    {
        $maxAmount = $item->getProductAvailableAmount();

        if ($item->isPersistent() && \XLite\Core\Request::getInstance()->oldAmount) {
            $maxAmount += \XLite\Core\Request::getInstance()->oldAmount;
            \XLite\Core\Request::getInstance()->oldAmount = null;
        }

        return array(
            'item_id'   => $item->getItemId(),
            'requestId' => \XLite\Core\Request::getInstance()->requestId,
            'price'     => $item->getNetPrice(),
            'max_qty'   => $maxAmount,
        );
    }

    /**
     * getViewerTemplate
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        $result = parent::getViewerTemplate();

        if ('invoice' === \XLite\Core\Request::getInstance()->mode) {
            $result = 'common/print_invoice.twig';
        }

        if ('packing_slip' === \XLite\Core\Request::getInstance()->mode) {
            $result = 'common/print_packing_slip.twig';
        }

        return $result;
    }

    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['default'] = static::t('General info');
        $list['invoice'] = static::t('Invoice');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['default'] = 'order/page/info.tab.twig';
        $list['invoice'] = 'order/page/invoice.twig';

        return $list;
    }

    // }}}

    // {{{ Edit order

    /**
     * Get order form
     *
     * @return \XLite\View\Order\Details\Admin\Form
     */
    public function getOrderForm()
    {
        if (!isset($this->orderForm)) {
            $this->orderForm = new \XLite\View\Order\Details\Admin\Model();
        }

        return $this->orderForm;
    }

    /**
     * Service method: Get attributes for order status field widget.
     *
     * @param string $statusType Status type ('payment' or 'shipping')
     *
     * @return array
     */
    public function getOrderStatusAttributes($statusType)
    {
        return array(
            'class' => 'not-affect-recalculate',
        );
    }

    // }}}

    // {{{ Order surcharges

    /**
     * Get order surcharge totals
     *
     * @param boolean $force Force reassmeble modifiers OPTIONAL
     *
     * @return array
     */
    public function getSurchargeTotals($force = false)
    {
        if (null === $this->modifiers || $force) {
            $this->modifiers = $this->defineSurchargeTotals();
        }

        return $this->modifiers;
    }

    /**
     * Define surcharge totals
     *
     * @return array
     */
    protected function defineSurchargeTotals()
    {
        return $this->postprocessSurchargeTotals(
            $this->getOrderForm()->getModelObject()->getCompleteSurchargeTotals()
        );
    }

    /**
     * Postprocess surcharge totals
     *
     * @param array $modifiers Modifiers
     *
     * @return array
     */
    protected function postprocessSurchargeTotals(array $modifiers)
    {
        foreach ($modifiers as $code => $modifier) {
            $method = $this->assembleMethodNameByCodeModifier('postprocess%sSurcharge', $code);
            if (method_exists($this, $method)) {
                $modifiers[$code] = $this->$method($modifier);
            }
        }

        foreach ($this->getRequiredSurcharges() as $code) {
            if (!isset($modifiers[$code])) {
                $method = $this->assembleMethodNameByCodeModifier('assemble%sDumpSurcharge', $code);
                $dump = $this->$method();
                if ($dump && !isset($modifiers[$dump['code']])) {
                    $modifiers[$dump['code']] = $dump;
                }
            }
        }

        return $modifiers;
    }

    /**
     * Assemble method name by modifier's code
     *
     * @param string $pattern Pattern
     * @param string $code    Modifier's code
     *
     * @return string
     */
    protected function assembleMethodNameByCodeModifier($pattern, $code)
    {
        $code = preg_replace('/[^a-zA-Z0-9]/S', '', ucfirst(strtolower($code)));

        return sprintf($pattern, $code);
    }

    /**
     * Assemble shipping dump surcharge
     *
     * @return array
     */
    protected function assembleShippingDumpSurcharge()
    {
        return $this->assembleDefaultDumpSurcharge(
            \XLite\Model\Base\Surcharge::TYPE_SHIPPING,
            \XLite\Logic\Order\Modifier\Shipping::MODIFIER_CODE,
            '\XLite\Logic\Order\Modifier\Shipping',
            static::t('Shipping cost')
        );
    }

    /**
     * Assemble default dump surcharge
     *
     * @param string $type  Type
     * @param string $code  Code
     * @param string $class Class
     * @param string $name  Name
     *
     * @return array
     */
    protected function assembleDefaultDumpSurcharge($type, $code, $class, $name)
    {
        $surcharge = new \XLite\Model\Order\Surcharge;
        $surcharge->setType($type);
        $surcharge->setCode($code);
        $surcharge->setClass($class);
        $surcharge->setValue(0);
        $surcharge->setName($name);
        $surcharge->setOwner(static::getTemporaryOrder($this->getOrder()->getOrderId(), false) ?: $this->getOrder());

        return array(
            'name'      => $surcharge->getTypeName(),
            'cost'      => $surcharge->getValue(),
            'available' => $surcharge->getAvailable(),
            'count'     => 1,
            'lastName'  => $surcharge->getName(),
            'code'      => $surcharge->getCode(),
            'widget'    => \Includes\Utils\Operator::checkIfClassExists($class)
                ? $class::getWidgetClass()
                : \XLite\Logic\Order\Modifier\AModifier::getWidgetClass(),
            'object'    => $surcharge,
        );
    }

    /**
     * Get required surcharges
     *
     * @return array
     */
    protected function getRequiredSurcharges()
    {
        return array(
            \XLite\Logic\Order\Modifier\Shipping::MODIFIER_CODE,
        );
    }

    // }}}
}
