<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * Class represents an order
 *
 * @Entity
 * @Table  (name="orders",
 *      indexes={
 *          @Index (name="date", columns={"date"}),
 *          @Index (name="total", columns={"total"}),
 *          @Index (name="subtotal", columns={"subtotal"}),
 *          @Index (name="tracking", columns={"tracking"}),
 *          @Index (name="payment_status", columns={"payment_status_id"}),
 *          @Index (name="shipping_status", columns={"shipping_status_id"}),
 *          @Index (name="shipping_id", columns={"shipping_id"}),
 *          @Index (name="lastRenewDate", columns={"lastRenewDate"}),
 *          @Index (name="orderNumber", columns={"orderNumber"})
 *      }
 * )
 *
 * @HasLifecycleCallbacks
 * @InheritanceType       ("SINGLE_TABLE")
 * @DiscriminatorColumn   (name="is_order", type="integer", length=1)
 * @DiscriminatorMap      ({1 = "XLite\Model\Order", 0 = "XLite\Model\Cart"})
 */
class Order extends \XLite\Model\Base\SurchargeOwner
{
    /**
     * Order total that is financially declared as zero (null)
     */
    const ORDER_ZERO = 0.00001;

    /**
     * Add item error codes
     */
    const NOT_VALID_ERROR = 'notValid';

    /**
     * Order unique id
     *
     * @var mixed
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $order_id;

    /**
     * Order profile
     *
     * @var \XLite\Model\Profile
     *
     * @OneToOne   (targetEntity="XLite\Model\Profile", cascade={"merge","detach","persist"})
     * @JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Original profile
     *
     * @var \XLite\Model\Profile
     *
     * @ManyToOne  (targetEntity="XLite\Model\Profile", cascade={"merge","detach","persist"})
     * @JoinColumn (name="orig_profile_id", referencedColumnName="profile_id", onDelete="SET NULL")
     */
    protected $orig_profile;

    /**
     * Shipping method unique id
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $shipping_id = 0;

    /**
     * Shipping method name
     *
     * @var integer
     *
     * @Column (type="string", nullable=true)
     */
    protected $shipping_method_name = '';

    /**
     * Payment method name
     *
     * @var integer
     *
     * @Column (type="string", nullable=true)
     */
    protected $payment_method_name = '';

    /**
     * Shipping tracking code
     *
     * @var string
     *
     * @Column (type="string", length=32)
     */
    protected $tracking = '';

    /**
     * Order creation timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

    /**
     * Last order renew date
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $lastRenewDate = 0;

    /**
     * Payment status
     *
     * @var \XLite\Model\Order\Status\Payment
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order\Status\Payment")
     * @JoinColumn (name="payment_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $paymentStatus;

    /**
     * Shipping status
     *
     * @var \XLite\Model\Order\Status\Shipping
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order\Status\Shipping")
     * @JoinColumn (name="shipping_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $shippingStatus;

    /**
     * Customer notes
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $notes = '';

    /**
     * Admin notes
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $adminNotes = '';

    /**
     * Order details
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\OrderDetail", mappedBy="order", cascade={"all"})
     * @OrderBy   ({"name" = "ASC"})
     */
    protected $details;

    /**
     * Order tracking numbers
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\OrderTrackingNumber", mappedBy="order", cascade={"all"})
     */
    protected $trackingNumbers;

    /**
     * Order events queue
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\OrderHistoryEvents", mappedBy="order", cascade={"all"})
     */
    protected $events;

    /**
     * Order items
     *
     * @var \Doctrine\Common\Collections\Collection|\XLite\Model\OrderItem[]
     *
     * @OneToMany (targetEntity="XLite\Model\OrderItem", mappedBy="order", cascade={"all"})
     */
    protected $items;

    /**
     * Order surcharges
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Order\Surcharge", mappedBy="owner", cascade={"all"})
     * @OrderBy   ({"weight" = "ASC", "id" = "ASC"})
     */
    protected $surcharges;

    /**
     * Payment transactions
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Model\Payment\Transaction", mappedBy="order", cascade={"all"})
     */
    protected $payment_transactions;

    /**
     * Currency
     *
     * @var \XLite\Model\Currency
     *
     * @ManyToOne  (targetEntity="XLite\Model\Currency", inversedBy="orders", cascade={"merge","detach"})
     * @JoinColumn (name="currency_id", referencedColumnName="currency_id", onDelete="CASCADE")
     */
    protected $currency;

    /**
     * Unique order identificator (it is working for orders only, not for cart entities)
     *
     * @var integer
     *
     * @Column (type="string", length=255, nullable=true)
     */
    protected $orderNumber;

    /**
     * Recent flag: true if order's statuses were not changed manually by an administrator, otherwise - false
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $recent = true;

    /**
     * 'Add item' error code
     *
     * @var string
     */
    protected $addItemError;

    /**
     * Order previous payment status
     *
     * @var \XLite\Model\Order\Status\Payment
     */
    protected $oldPaymentStatus;

    /**
     * Flag: true - order is prepared for removing
     *
     * @var boolean
     */
    protected $isRemoving = false;

    /**
     * Order previous shipping status
     *
     * @var \XLite\Model\Order\Status\Shipping
     */
    protected $oldShippingStatus;

    /**
     * Modifiers (cache)
     *
     * @var \XLite\DataSet\Collection\OrderModifier
     */
    protected $modifiers;

    /**
     * Shipping carrier object cache.
     * Use $this->getShippingProcessor() method to retrieve.
     *
     * @var \XLite\Model\Shipping\Processor\AProcessor
     */
    protected $shippingProcessor;

    /**
     * Check if notification sent by any status handler to avoid extra 'changed' notification
     *
     * @var boolean
     */
    protected $isNotificationSent = false;

    /**
     * Flag: Ignore sending notifications to a customer if true
     *
     * @var boolean
     */
    protected $ignoreCustomerNotifications = false;

    /**
     * Flag: is email notifications are allowed for the order
     *
     * @var boolean
     */
    protected $isNotificationsAllowedFlag = true;

    /**
     * Check status is set or not
     *
     * @var boolean
     */
    protected $statusIsSet = false;

    /**
     * Payment transaction sums
     *
     * @var array
     */
    protected $paymentTransactionSums;

    /**
     * Source address
     *
     * @var \XLite\Model\Address
     */
    protected $sourceAddress;

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $xcPendingExport = false;

    /**
     * If entity temporary
     *
     * @var bool
     */
    protected $temporary = false;

    /**
     * @return boolean
     */
    public function isTemporary()
    {
        return $this->temporary;
    }

    /**
     * @param boolean $temporary
     */
    public function setTemporary($temporary)
    {
        $this->temporary = $temporary;
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
        $result = false;

        if ($newItem->isValid() && $newItem->isConfigured()) {
            $this->addItemError = null;
            $newItem->setOrder($this);

            $item = $this->getItemByItem($newItem);

            $warningText = '';

            if ($item) {
                $item->setAmount($item->getAmount() + $newItem->getAmount());

                $warningText = $item->getAmountWarning(
                    $item->getAmount() + $newItem->getAmount()
                );

            } else {
                $this->addItems($newItem);
            }

            $result = '' === $warningText;

            if ('' !== $warningText) {
                \XLite\Core\TopMessage::addWarning($warningText);
            }

        } else {
            $this->addItemError = self::NOT_VALID_ERROR;
        }

        return $result;
    }

    /**
     * Get 'Add item' error code
     *
     * @return string|void
     */
    public function getAddItemError()
    {
        return $this->addItemError;
    }

    /**
     * Get item from order by another item
     *
     * @param \XLite\Model\OrderItem $item Another item
     *
     * @return \XLite\Model\OrderItem|void
     */
    public function getItemByItem(\XLite\Model\OrderItem $item)
    {
        return $this->getItemByItemKey($item->getKey());
    }

    /**
     * Get item from order by item key
     *
     * @param string $key Item key
     *
     * @return \XLite\Model\OrderItem|void
     */
    public function getItemByItemKey($key)
    {
        $items = $this->getItems();

        return \Includes\Utils\ArrayManager::findValue(
            $items,
            array($this, 'checkItemKeyEqual'),
            $key
        );
    }

    /**
     * Get item from order by item  id
     *
     * @param integer $itemId Item id
     *
     * @return \XLite\Model\OrderItem|void
     */
    public function getItemByItemId($itemId)
    {
        $items = $this->getItems();

        return \Includes\Utils\ArrayManager::findValue(
            $items,
            array($this, 'checkItemIdEqual'),
            $itemId
        );
    }

    /**
     * Find items by product ID
     *
     * @param integer $productId Product ID to use
     *
     * @return array
     */
    public function getItemsByProductId($productId)
    {
        $items = $this->getItems();

        return \Includes\Utils\ArrayManager::filter(
            $items,
            array($this, 'isItemProductIdEqual'),
            $productId
        );
    }

    /**
     * Normalize items
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function normalizeItems()
    {
        // Normalize by key
        $keys = array();

        foreach ($this->getItems() as $item) {
            $key = $item->getKey();
            if (isset($keys[$key])) {
                $keys[$key]->setAmount($keys[$key]->getAmount() + $item->getAmount());
                $this->getItems()->removeElement($item);

                if (\XLite\Core\Database::getEM()->contains($item)) {
                    \XLite\Core\Database::getEM()->remove($item);
                }

            } else {
                $keys[$key] = $item;
            }
        }

        unset($keys);

        // Remove invalid items
        foreach ($this->getItems() as $item) {
            if (!$item->isValid()) {
                $this->getItems()->removeElement($item);
                if (\XLite\Core\Database::getEM()->contains($item)) {
                    \XLite\Core\Database::getEM()->remove($item);
                }
            }
        }
    }

    /**
     * Return items number
     *
     * @return integer
     */
    public function countItems()
    {
        return count($this->getItems());
    }

    /**
     * Return order items total quantity
     *
     * @return integer
     */
    public function countQuantity()
    {
        $quantity = 0;

        foreach ($this->getItems() as $item) {
            $quantity += $item->getAmount();
        }

        return $quantity;
    }

    /**
     * Get failure reason
     *
     * @return string
     */
    public function getFailureReason()
    {
        $transactions = $this->getPaymentTransactions()->getValues();
        /** @var \XLite\Model\Payment\Transaction $transaction */
        foreach (array_reverse($transactions) as $transaction) {
            if ($transaction->isFailed()) {
                $reason = $transaction->getDataCell('status');

                if ($reason && $reason->getValue()) {
                    return $reason->getValue();
                }
            }
        }

        return null;
    }

    /**
     * Checks whether the shopping cart/order is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return 0 >= $this->countItems();
    }

    /**
     * Check order subtotal
     *
     * @return boolean
     */
    public function isMinOrderAmountError()
    {
        return $this->getSubtotal() < \XLite\Core\Config::getInstance()->General->minimal_order_amount;
    }

    /**
     * Check order subtotal
     *
     * @return boolean
     */
    public function isMaxOrderAmountError()
    {
        return $this->getSubtotal() > \XLite\Core\Config::getInstance()->General->maximal_order_amount;
    }

    /**
     * Return printable order number
     *
     * @return string
     */
    public function getPrintableOrderNumber()
    {
        $orderNumber = $this->getOrderNumber();

        return '#' . str_repeat('0', 5 - min(5, strlen($orderNumber))) . $orderNumber;
    }

    /**
     * Check - is order processed or not
     *
     * @return boolean
     */
    public function isProcessed()
    {
        return in_array(
            $this->getPaymentStatusCode(),
            array(
                \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
                \XLite\Model\Order\Status\Payment::STATUS_PAID,
            ),
            true
        );
    }

    /**
     * Check - is order queued or not
     *
     * @return boolean
     */
    public function isQueued()
    {
        return $this->getPaymentStatusCode() === \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
    }

    /**
     * Check item amounts
     *
     * @return array
     */
    public function getItemsWithWrongAmounts()
    {
        $items = array();
        foreach ($this->getItems() as $item) {
            if ($item->hasWrongAmount()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Set profile
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function setProfile(\XLite\Model\Profile $profile = null)
    {
        if ($this->getProfile()
            && $this->getProfile()->getOrder()
            && (!$profile || $this->getProfile()->getProfileId() != $profile->getProfileId())
        ) {
            $this->getProfile()->setOrder(null);
            if ($this->getProfile()->getAnonymous() && $profile && !$profile->getAnonymous()) {
                \XLite\Core\Database::getEM()->remove($this->getProfile());
            }
        }

        $this->profile = $profile;
    }

    /**
     * Set original profile
     * FIXME: is it really needed?
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return void
     */
    public function setOrigProfile(\XLite\Model\Profile $profile = null)
    {
        if ($this->getOrigProfile()
            && $this->getOrigProfile()->getOrder()
            && (!$profile || $this->getOrigProfile()->getProfileId() != $profile->getProfileId())
            && (!$this->getProfile() || $this->getOrigProfile()->getProfileId() != $this->getProfile()->getProfileId())
        ) {
            $this->getOrigProfile()->setOrder(null);
        }

        $this->orig_profile = $profile;
    }

    /**
     * Set profile copy
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return void
     */
    public function setProfileCopy(\XLite\Model\Profile $profile)
    {
        // Set profile as original profile
        $this->setOrigProfile($profile);

        // Clone profile and set as order profile
        $clonedProfile = $profile->cloneEntity();
        $this->setProfile($clonedProfile);
        $clonedProfile->setOrder($this);
    }

    // {{{ Clone

    /**
     * clone Order as temporary
     *
     * @return \XLite\Model\Order
     */
    public function cloneOrderAsTemporary()
    {
        $isTemporary = $this->isTemporary();
        $this->setTemporary(true);
        $result = $this->cloneEntity();
        $result->setOrderNumber(null);
        $result->setIsNotificationsAllowedFlag(null);
        $result->setTemporary(true);
        $this->setTemporary($isTemporary);

        return $result;
    }

    /**
     * Clone order and all related data
     * TODO: Decompose this method into several methods
     *
     * @return \XLite\Model\Order
     */
    public function cloneEntity()
    {
        // Clone order
        $newOrder = parent::cloneEntity();

        if ($this->getOrderNumber() && !$this->isTemporary()) {
            $newOrder->setOrderNumber(
                \XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber()
            );
        }
        
        // Clone profile
        $newOrder->setOrigProfile($this->getOrigProfile());

        if ($this->getProfile()) {
            $clonedProfile = $this->getProfile()->cloneEntity();
            $newOrder->setProfile($clonedProfile);
            $clonedProfile->setOrder($newOrder);
        }

        // Clone order statuses
        $newOrder->setPaymentStatus($this->getPaymentStatus());
        $newOrder->setShippingStatus($this->getShippingStatus());

        // Clone currency
        $newOrder->setCurrency($this->getCurrency());

        // Clone order items
        $this->cloneOrderItems($newOrder);

        // Clone order details
        foreach ($this->getDetails() as $detail) {
            $clonedDetails = $detail->cloneEntity();
            $newOrder->addDetails($clonedDetails);
            $clonedDetails->setOrder($newOrder);
        }

        // Clone tracking numbers
        foreach ($this->getTrackingNumbers() as $tn) {
            $clonedTN = $tn->cloneEntity();
            $newOrder->addTrackingNumbers($clonedTN);
            $clonedTN->setOrder($newOrder);
        }

        // Clone events
        foreach ($this->getEvents() as $event) {
            $cloned = $event->cloneEntity();
            $newOrder->addEvents($cloned);
            $cloned->setOrder($newOrder);
            $cloned->setAuthor($event->getAuthor());
        }

        // Clone surcharges
        foreach ($this->getSurcharges() as $surcharge) {
            $cloned = $surcharge->cloneEntity();
            $newOrder->addSurcharges($cloned);
            $cloned->setOwner($newOrder);
        }

        // Clone payment transactions
        foreach ($this->getPaymentTransactions() as $pt) {
            $cloned = $pt->cloneEntity();
            $newOrder->addPaymentTransactions($cloned);
            $cloned->setOrder($newOrder);
        }

        return $newOrder;
    }

    /**
     * Clone order items
     *
     * @param \XLite\Model\Order $newOrder New Order
     *
     * @return void
     */
    protected function cloneOrderItems($newOrder)
    {
        // Clone order items
        foreach ($this->getItems() as $item) {
            $clonedItem = $item->cloneEntity();
            $clonedItem->setOrder($newOrder);
            $newOrder->addItems($clonedItem);
        }
    }

    // }}}

    /**
     * Get shipping method name
     *
     * @return string
     */
    public function getShippingMethodName()
    {
        $shipping = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->getShippingId());

        return $shipping ? $shipping->getName() : $this->shipping_method_name;
    }

    /**
     * Get payment method name
     *
     * @return string
     */
    public function getPaymentMethodName()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod ? $paymentMethod->getName() : $this->payment_method_name;
    }

    /**
     * Get payment method name
     *
     * @return string
     */
    public function getPaymentMethodId()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod ? $paymentMethod->getMethodId() : null;
    }

    /**
     * Set old payment status of the order (not stored in the DB)
     *
     * @param string $paymentStatus Payment status
     *
     * @return void
     */
    public function setOldPaymentStatus($paymentStatus)
    {
        $this->oldPaymentStatus = $paymentStatus;
    }

    /**
     * Set old shipping status of the order (not stored in the DB)
     *
     * @param string $shippingStatus Shipping status
     *
     * @return void
     */
    public function setOldShippingStatus($shippingStatus)
    {
        $this->oldShippingStatus = $shippingStatus;
    }

    /**
     * Get items list fingerprint
     *
     * @return string
     */
    public function getItemsFingerprint()
    {
        $result = false;

        if (!$this->isEmpty()) {
            $items = array();
            foreach ($this->getItems() as $item) {
                $items[] = array(
                    $item->getItemId(),
                    $item->getKey(),
                    $item->getAmount()
                );
            }

            $result = md5(serialize($items));
        }

        return $result;
    }

    /**
     * Get fingerprint of cart items corresponding to specific product id
     *
     * @return string
     */
    public function getItemsFingerprintByProductId($productId)
    {
        $result = false;

        if (!$this->isEmpty()) {
            $items = array();
            foreach ($this->getItems() as $item) {
                if ($item->getObject() && $item->getObject()->getId() == $productId)
                    $items[] = array(
                        $item->getItemId(),
                        $item->getKey(),
                        $item->getAmount(),
                    );
            }

            $result = md5(serialize($items));
        }

        return $result;
    }

    /**
     * Generate a string representation of the order
     * to send to a payment service
     *
     * @return string
     */
    public function getDescription()
    {
        $result = array();

        foreach ($this->getItems() as $item) {
            $result[] = $item->getDescription();
        }

        return implode("\n", $result);
    }

    /**
     * Get order fingerprint for event subsystem
     *
     * @param array $exclude Exclude kes OPTIONAL
     *
     * @return array
     */
    public function getEventFingerprint(array $exclude = array())
    {
        $keys = array_diff($this->defineFingerprintKeys(), $exclude);

        $hash = array();
        foreach ($keys as $key) {
            $method = 'getFingerprintBy' . ucfirst($key);
            $hash[$key] = $this->$method();
        }

        return $hash;
    }

    /**
     * Returns delivery source address
     *
     * @return \XLite\Model\Address
     */
    public function getSourceAddress()
    {
        if (null === $this->sourceAddress) {
            $address = new \XLite\Model\Address();

            $config = $this->getCompanyConfiguration();

            $address->setStreet($config->origin_address);
            $address->setCity($config->origin_city);
            $address->setCountryCode($config->origin_country);

            if ($config->origin_state) {
                $address->setStateId($config->origin_state);
            }

            if ($config->origin_custom_state) {
                $address->setCustomState($config->origin_custom_state);
            }

            $address->setZipcode($config->origin_zipcode);

            $this->sourceAddress = $address;
        }

        return $this->sourceAddress;
    }

    /**
     * Returns company configuration
     *
     * @return \XLite\Core\ConfigCell
     */
    protected function getCompanyConfiguration()
    {
        return \XLite\Core\Config::getInstance()->Company;
    }

    /**
     * Define fingerprint keys
     *
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        return array(
            'items',
            'itemsCount',
            'shippingTotal',
            'total',
            'shippingMethodId',
            'paymentMethodId',
            'shippingMethodsHash',
            'paymentMethodsHash',
            'shippingAddressId',
            'billingAddressId',
            'shippingAddressFields',
            'billingAddressFields',
            'sameAddress',
        );
    }

    /**
     * Get fingerprint by 'items' key
     *
     * @return array
     */
    protected function getFingerprintByItems()
    {
        $list = array();

        foreach ($this->getItems() as $item) {
            $event = $item->getEventCell();
            $event['quantity'] = $item->getAmount();

            if ($this->isItemLimitReached($item)) {
                $event['is_limit'] = 1;
            }

            $list[] = $event;
        }

        return $list;
    }

    /**
     * Return true if item amount limit is reached
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isItemLimitReached($item)
    {
        $result = false;

        $object = $item->getObject();

        if ($object) {
            $result = $object->getInventoryEnabled() && $object->getPublicAmount() <= $item->getAmount();
        }

        return $result;
    }

    /**
     * Get fingerprint by 'itemsCount' key
     *
     * @return array
     */
    protected function getFingerprintByItemsCount()
    {
        return $this->countQuantity();
    }

    /**
     * Get fingerprint by 'shippingTotal' key
     *
     * @return float
     */
    protected function getFingerprintByShippingTotal()
    {
        $shippingModifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $shippingModifier && $shippingModifier->getSelectedRate()
            ? $shippingModifier->getSelectedRate()->getTotalRate()
            : 0;
    }

    /**
     * Get fingerprint by 'total' key
     *
     * @return float
     */
    protected function getFingerprintByTotal()
    {
        return $this->getTotal();
    }

    /**
     * Get fingerprint by 'shippingMethodId' key
     *
     * @return integer
     */
    protected function getFingerprintByShippingMethodId()
    {
        $shippingModifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $shippingModifier && $shippingModifier->getSelectedRate()
            ? $shippingModifier->getSelectedRate()->getMethod()->getMethodId()
            : 0;
    }

    /**
     * Get fingerprint by 'paymentMethodId' key
     *
     * @return integer
     */
    protected function getFingerprintByPaymentMethodId()
    {
        return $this->getPaymentMethod()
                ? $this->getPaymentMethod()->getMethodId()
                : 0;
    }

    /**
     * Get fingerprint by 'shippingMethodsHash' key
     *
     * @return string
     */
    protected function getFingerprintByShippingMethodsHash()
    {
        $shippingModifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        $shippingMethodsHash = array();
        if ($shippingModifier) {
            foreach ($shippingModifier->getRates() as $rate) {
                $shippingMethodsHash[] = $rate->getMethod()->getMethodId() . ':' . $rate->getTotalRate();
            }
        }

        return implode(';', $shippingMethodsHash);
    }

    /**
     * Get fingerprint by 'paymentMethodsHash' key
     *
     * @return string
     */
    protected function getFingerprintByPaymentMethodsHash()
    {
        $paymentMethodsHash = array();
        foreach ($this->getPaymentMethods() as $method) {
            $paymentMethodsHash[] = $method->getMethodId();
        }

        return implode(';', $paymentMethodsHash);
    }

    /**
     * Get fingerprint by 'shippingAddressId' key
     *
     * @return integer
     */
    protected function getFingerprintByShippingAddressId()
    {
        return $this->getProfile() && $this->getProfile()->getShippingAddress()
            ? $this->getProfile()->getShippingAddress()->getAddressId()
            : 0;
    }

    /**
     * Get fingerprint by 'billingAddressId' key
     *
     * @return integer
     */
    protected function getFingerprintByBillingAddressId()
    {
        return $this->getProfile() && $this->getProfile()->getBillingAddress()
            ? $this->getProfile()->getBillingAddress()->getAddressId()
            : 0;
    }


    /**
     * Get fingerprint by 'shippingAddressId' key
     *
     * @return integer
     */
    protected function getFingerprintByShippingAddressFields()
    {
        if ($this->getProfile() && $this->getProfile()->getShippingAddress()) {
            return $this->getProfile()->getShippingAddress()->serialize();
        }

        return '';
    }

    /**
     * Get fingerprint by 'billingAddressId' key
     *
     * @return integer
     */
    protected function getFingerprintByBillingAddressFields()
    {
        if ($this->getProfile() && $this->getProfile()->getBillingAddress()) {
            return $this->getProfile()->getBillingAddress()->serialize();
        }

        return '';
    }


    /**
     * Get fingerprint by 'sameAddress' key
     *
     * @return boolean
     */
    protected function getFingerprintBySameAddress()
    {
        return $this->getProfile() && $this->getProfile()->isSameAddress();
    }

    /**
     * Get detail
     *
     * @param string $name Details cell name
     *
     * @return mixed
     */
    public function getDetail($name)
    {
        $details = $this->getDetails();
        return \Includes\Utils\ArrayManager::findValue(
            $details,
            array($this, 'checkDetailName'),
            $name
        );
    }

    /**
     * Get detail value
     *
     * @param string $name Details cell name
     *
     * @return mixed
     */
    public function getDetailValue($name)
    {
        $detail = $this->getDetail($name);

        return $detail ? $detail->getValue() : null;
    }

    /**
     * Set detail cell
     * To unset detail cell the value should be null
     *
     * @param string $name  Cell code
     * @param mixed  $value Cell value
     * @param string $label Cell label OPTIONAL
     *
     * @return void
     */
    public function setDetail($name, $value, $label = null)
    {
        $detail = $this->getDetail($name);

        if (is_null($value)) {
            if ($detail) {
                $this->getDetails()->removeElement($detail);
                \XLite\Core\Database::getEM()->remove($detail);
            }

        } else {
            if (!$detail) {
                $detail = new \XLite\Model\OrderDetail();

                $detail->setOrder($this);
                $this->addDetails($detail);

                $detail->setName($name);
            }

            $detail->setValue($value);
            $detail->setLabel($label);
        }
    }

    /**
     * Get meaning order details
     *
     * @return array
     */
    public function getMeaningDetails()
    {
        $result = array();

        foreach ($this->getDetails() as $detail) {
            if ($detail->getLabel()) {
                $result[] = $detail;
            }
        }

        return $result;
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        $this->markAsOrder();

        if ($this->canChangeStatusOnSucceed()) {
            $this->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_NEW);
        }

        $property = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Property')->findOneBy(
            array(
                'paymentStatus'  => $this->getPaymentStatus(),
                'shippingStatus' => $this->getShippingStatus(),
            )
        );
        $incStock = $property ? $property->getIncStock() : null;
        if (false === $incStock) {
            $this->decreaseInventory();
        }

        // Transform attributes
        $this->transformItemsAttributes();

        $this->sendOrderCreatedIfNeeded();
    }

    /**
     * Send order created notification if needed
     */
    public function sendOrderCreatedIfNeeded()
    {
        if ($this->getPaymentStatus()
            && in_array(
                $this->getPaymentStatus()->getCode(),
                $this->getValidStatusesForCreateNotification(),
                true
            )
        ) {
            \XLite\Core\Mailer::getInstance()->sendOrderCreated($this);
        }
    }


    /**
     * Valid not paid statuses
     *
     * @return array
     */
    public function getValidStatusesForCreateNotification()
    {
        return [
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
        ];
    }

    /**
     * Mark cart as order
     *
     * @return void
     */
    public function markAsOrder()
    {
    }

    /**
     * Mark order as cart
     *
     * @return void
     */
    public function markAsCart()
    {
        $this->getRepository()->markAsCart($this->getOrderId());
    }

    /**
     * Refresh order items
     * TODO - rework after tax subsystem rework
     *
     * @return void
     */
    public function refreshItems()
    {
    }

    /**
     * Return removing status
     *
     * @return boolean
     */
    public function isRemoving()
    {
        return $this->isRemoving;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = array())
    {
        $this->details              = new \Doctrine\Common\Collections\ArrayCollection();
        $this->items                = new \Doctrine\Common\Collections\ArrayCollection();
        $this->surcharges           = new \Doctrine\Common\Collections\ArrayCollection();
        $this->payment_transactions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events               = new \Doctrine\Common\Collections\ArrayCollection();
        $this->trackingNumbers      = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return list of available payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        if (0 < $this->getOpenTotal()) {
            $list = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findAllActive();

            foreach ($list as $i => $method) {
                if (!$method->isEnabled() || !$method->getProcessor()->isApplicable($this, $method)) {
                    unset($list[$i]);
                }
            }

        } else {
            $list = array();
        }

        if (\XLite\Core\Auth::getInstance()->isOperatingAsUserMode()) {
            $fakeMethods = \XLite\Core\Auth::getInstance()->getOperateAsUserPaymentMethods();

            $filteredFakeMethods = array_filter(
                $fakeMethods,
                function ($fakeMethod) use ($list) {
                    $fakeServiceName = $fakeMethod->getServiceName();

                    // Check if $list already contains fake method
                    return !array_reduce($list, function ($carry, $method) use ($fakeServiceName) {
                        return $carry ?: $method->getServiceName() === $fakeServiceName;
                    }, false);
                }
            );

            $list = array_merge(
                $list,
                $filteredFakeMethods
            );
        }

        return $list;
    }

    /**
     * Renew payment method
     *
     * @return void
     */
    public function renewPaymentMethod()
    {
        if ($this->isPaymentMethodRequired()) {
            $method = $this->getPaymentMethod();

            if ($this->isPaymentMethodIsApplicable($method)) {
                $this->setPaymentMethod($method);

            } else {
                $first = $this->getFirstPaymentMethod();
                if ($first) {
                    if ($this->getProfile()) {
                        $this->getProfile()->setLastPaymentId($first->getMethodId());
                    }

                    $this->setPaymentMethod($first);

                } else {
                    $this->unsetPaymentMethod();
                }
            }

        } else {
            $this->unsetPaymentMethod();
        }
    }

    /**
     * Returns true if order is allowed to change status on succeed
     *
     * @return boolean
     */
    protected function canChangeStatusOnSucceed()
    {
        return null === $this->getShippingStatus();
    }

    /**
     * Return true if payment method is required for the order
     *
     * @return boolean
     */
    protected function isPaymentMethodRequired()
    {
        return 0 < $this->getOpenTotal()
            && ($this instanceof \XLite\Model\Cart || $this->getOrderNumber() === null);
    }

    /**
     * Check if given payment method can be applied to the order
     * 
     * @param  \XLite\Model\Payment\Method  $method
     * @return boolean
     */
    protected function isPaymentMethodIsApplicable($method)
    {
        return $method 
            && $method->getProcessor() 
            && $method->getProcessor()->isApplicable($this, $method);
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method|null
     */
    public function getPaymentMethod()
    {
        $transaction = $this->getFirstOpenPaymentTransaction();
        if (!$transaction) {
            $transaction = $this->hasUnpaidTotal() || 0 === count($this->getPaymentTransactions())
                ? $this->assignLastPaymentMethod()
                : $this->getPaymentTransactions()->last();
        }

        return $transaction ? $transaction->getPaymentMethod() : null;
    }

    /**
     * Check item key equal
     *
     * @param \XLite\Model\OrderItem $item Item
     * @param string                 $key  Key
     *
     * @return boolean
     */
    public function checkItemKeyEqual(\XLite\Model\OrderItem $item, $key)
    {
        return $item->getKey() == $key;
    }

    /**
     * Check item id equal
     *
     * @param \XLite\Model\OrderItem $item   Item
     * @param integer                $itemId Item id
     *
     * @return boolean
     */
    public function checkItemIdEqual(\XLite\Model\OrderItem $item, $itemId)
    {
        return $item->getItemId() == $itemId;
    }

    /**
     * Check order detail name
     *
     * @param \XLite\Model\OrderDetail $detail Detail
     * @param string                   $name   Name
     *
     * @return boolean
     */
    public function checkDetailName(\XLite\Model\OrderDetail $detail, $name)
    {
        return $detail->getName() === $name;
    }

    /**
     * Check payment transaction status
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param mixed                            $status      Status
     *
     * @return boolean
     */
    public function checkPaymentTransactionStatusEqual(\XLite\Model\Payment\Transaction $transaction, $status)
    {
        return is_array($status)
            ? in_array($transaction->getStatus(), $status, true)
            : $transaction->getStatus() === $status;
    }

    /**
     * Check payment transaction - open or not
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return boolean
     */
    public function checkPaymentTransactionOpen(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isOpen() || $transaction->isInProgress();
    }

    /**
     * Check - is item product id equal specified product id
     *
     * @param \XLite\Model\OrderItem $item      Item
     * @param integer                $productId Product id
     *
     * @return boolean
     */
    public function isItemProductIdEqual(\XLite\Model\OrderItem $item, $productId)
    {
        return $item->getProduct()->getProductId() == $productId;
    }

    /**
     * Check last payment method
     *
     * @param \XLite\Model\Payment\Method $pmethod       Payment method
     * @param integer                     $lastPaymentId Last selected payment method id
     *
     * @return boolean
     */
    public function checkLastPaymentMethod(\XLite\Model\Payment\Method $pmethod, $lastPaymentId)
    {
        $result = $pmethod->getMethodId() == $lastPaymentId;
        if ($result) {
            $this->setPaymentMethod($pmethod);
        }

        return $result;
    }

    // {{{ Payment method and transactions

    /**
     * Set payment method
     *
     * @param \XLite\Model\Payment\Method|null $paymentMethod Payment method
     * @param float                            $value         Payment transaction value OPTIONAL
     *
     * @return void
     */
    public function setPaymentMethod($paymentMethod, $value = null)
    {
        if ($paymentMethod && !($paymentMethod instanceof \XLite\Model\Payment\Method)) {
            $paymentMethod = null;
        }

        if (null === $paymentMethod || $this->getFirstOpenPaymentTransaction()) {
            $transaction = $this->getFirstOpenPaymentTransaction();
            if ($transaction) {
                if (null === $paymentMethod) {
                    $this->unsetPaymentMethod();

                } elseif ($transaction->isSameMethod($paymentMethod)) {
                    $transaction->updateValue($this);
                    $paymentMethod = null;
                }
            }
        }

        if (null !== $paymentMethod) {
            $this->unsetPaymentMethod();
            $this->addPaymentTransaction($paymentMethod, $value);
            $this->setPaymentMethodName($paymentMethod->getName());
        }
    }

    /**
     * Unset payment method
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function unsetPaymentMethod()
    {
        $transaction = $this->getFirstOpenPaymentTransaction();

        if ($transaction) {
            $this->getPaymentTransactions()->removeElement($transaction);
            \XLite\Core\Database::getEM()->remove($transaction);
        }
    }

    /**
     * Get active payment transactions
     *
     * @return array
     */
    public function getActivePaymentTransactions()
    {
        $result = array();
        foreach ($this->getPaymentTransactions() as $t) {
            if ($t->isCompleted() || $t->isPending()) {
                $result[] = $t;
            }
        }

        return $result;
    }

    /**
     * Get visible payment methods
     *
     * @return array
     */
    public function getVisiblePaymentMethods()
    {
        $result = array();

        foreach ($this->getActivePaymentTransactions() as $t) {
            if ($t->getPaymentMethod()) {
                $result[] = $t->getPaymentMethod();
            }
        }

        if (0 === count($result) && 0 < count($this->getPaymentTransactions())) {
            $method = $this->getPaymentTransactions()->last()->getPaymentMethod();
            if ($method) {
                $result[] = $method;
            }
        }

        return $result;
    }

    /**
     * Get first open (not payed) payment transaction
     *
     * @return \XLite\Model\Payment\Transaction|null
     */
    public function getFirstOpenPaymentTransaction()
    {
        $transactions = $this->getPaymentTransactions();

        return \Includes\Utils\ArrayManager::findValue(
            $transactions,
            array($this, 'checkPaymentTransactionOpen')
        );
    }

    /**
     * Get open (not-payed) total
     *
     * @return float
     */
    public function getOpenTotal()
    {
        $total = $this->getCurrency()->roundValue($this->getTotal());
        $totalAsSurcharges = $this->getCurrency()->roundValue($this->getSurchargesTotal());

        $totalsPaid = $this->getPaidTotals();

        return max(
            $total - $totalsPaid['total'],
            $totalAsSurcharges - $totalsPaid['totalAsSurcharges']
        );
    }

    public function getPaidTotals()
    {
        $total = $totalAsSurcharges = 0;

        foreach ($this->getPaymentTransactions() as $t) {
            $total += $this->getCurrency()->roundValue($t->getChargeValueModifier());
            $totalAsSurcharges += $this->getCurrency()->roundValue($t->getChargeValueModifier());
        }

        return array(
            'total'             => $total,
            'totalAsSurcharges' => $totalAsSurcharges,
        );
    }

    /**
     * Get paid total
     *
     * @return float
     */
    public function getPaidTotal()
    {
        $totals = $this->getPaidTotals();

        return max(
            $totals['total'],
            $totals['totalAsSurcharges']
        );
    }

    /**
     * Check - order is open (has initialized transactions and has open total) or not
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->getFirstOpenPaymentTransaction() && $this->hasUnpaidTotal();
    }

    /**
     * Has unpaid total?
     *
     * @return boolean
     */
    public function hasUnpaidTotal()
    {
        return $this->getCurrency()->getMinimumValue() < $this->getCurrency()->roundValue(abs($this->getOpenTotal()));
    }

    /**
     * Get totally payed total
     *
     * @return float
     */
    public function getPayedTotal()
    {
        $total = $this->getCurrency()->roundValue($this->getTotal());

        foreach ($this->getPaymentTransactions() as $t) {
            if ($t->isCompleted() && ($t->isAuthorized() || $t->isCaptured())) {
                $total -= $this->getCurrency()->roundValue($t->getChargeValueModifier());
            }
        }

        $sums = $this->getRawPaymentTransactionSums();
        $total += $sums['refunded'];

        return $total;
    }

    /**
     * Check - order is payed or not
     * Payed - order has not open total and all payment transactions are failed or completed
     *
     * @return boolean
     */
    public function isPayed()
    {
        return 0 >= $this->getPayedTotal();
    }

    /**
     * Check - order has in-progress payments or not
     *
     * @return boolean
     */
    public function hasInprogressPayments()
    {
        $result = false;

        foreach ($this->getPaymentTransactions() as $t) {
            if ($t->isInProgress()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Assign last used payment method
     *
     * @return \XLite\Model\Payment\Transaction|void
     */
    protected function assignLastPaymentMethod()
    {
        $found = null;

        if ($this->isPaymentMethodRequired() && $this->getProfile() && $this->getProfile()->getLastPaymentId()) {
            $paymentMethods = $this->getPaymentMethods();
            $found = \Includes\Utils\ArrayManager::findValue(
                $paymentMethods,
                array($this, 'checkLastPaymentMethod'),
                $this->getProfile()->getLastPaymentId()
            );
        }

        return $found ? $this->getFirstOpenPaymentTransaction() : null;
    }

    /**
     * Get first applicable payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getFirstPaymentMethod()
    {
        $list = $this->getPaymentMethods();

        return $list ? array_shift($list) : null;
    }

    /**
     * Add payment transaction
     * FIXME: move logic into \XLite\Model\Payment\Transaction
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     * @param float                       $value  Value OPTIONAL
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    protected function addPaymentTransaction(\XLite\Model\Payment\Method $method, $value = null)
    {
        if (null === $value || 0 >= $value) {
            $value = $this->getOpenTotal();

        } else {
            $value = min($value, $this->getOpenTotal());
        }

        // Do not add 0 or <0 transactions. This is for a "Payment not required" case.
        if ($value > 0) {
            $transaction = new \XLite\Model\Payment\Transaction();

            $this->addPaymentTransactions($transaction);
            $transaction->setOrder($this);

            $transaction->setPaymentMethod($method);

            \XLite\Core\Database::getEM()->persist($method);

            $transaction->setCurrency($this->getCurrency());

            $transaction->setStatus($transaction::STATUS_INITIALIZED);
            $transaction->setValue($value);
            $transaction->setType($method->getProcessor()->getInitialTransactionType($method));

            if ($method->getProcessor()->isTestMode($method)) {
                $transaction->setDataCell(
                    'test_mode',
                    true,
                    'Test mode'
                );
            }

            \XLite\Core\Database::getEM()->persist($transaction);
        }
    }

    // }}}

    // {{{ Shipping

    /**
     * Returns last shipping method id
     *
     * @return integer|null
     */
    public function getLastShippingId()
    {
        /** @var \XLite\Model\Profile $profile */
        $profile = $this->getProfile();

        return $profile ? $profile->getLastShippingId() : null;
    }

    /**
     * Sets last shipping method id used
     *
     * @param integer $value Method id
     *
     * @return void
     */
    public function setLastShippingId($value)
    {
        /** @var \XLite\Model\Profile $profile */
        $profile = $this->getProfile();

        if (null !== $profile) {
            $profile->setLastShippingId($value);
        }
    }

    /**
     * Renew shipping method
     *
     * @return void
     */
    public function renewShippingMethod()
    {
        /** @var \XLite\Logic\Order\Modifier\Shipping $modifier */
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        if ($modifier) {
            $this->setShippingId(0);
            if (null === $modifier->getSelectedRate()) {
                $method = $this->getFirstShippingMethod();
                if ($method) {
                    $methodId = $method->getMethodId();

                    $this->setLastShippingId($methodId);
                    $this->setShippingId($methodId);
                }
            }
        }
    }

    /**
     * Get the link for the detailed tracking information
     *
     * @return boolean|\XLite\Model\Shipping\Processor\AProcessor False if the shipping is not set or
     *                                                            shipping processor is absent
     */
    public function getShippingProcessor()
    {
        if ($this->shippingProcessor === null) {
            $shipping = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find($this->getShippingId());
            $this->shippingProcessor = $shipping ? ($shipping->getProcessorObject() ?: false) : false;
        }

        return $this->shippingProcessor;
    }

    /**
     * Defines whether the form must be used for tracking information.
     * The 'getTrackingInformationURL' result will be used as tracking link instead
     *
     * @param string $trackingNumber Tracking number value
     *
     * @return boolean
     */
    public function isTrackingInformationForm($trackingNumber)
    {
        return $this->getShippingProcessor()
            ? $this->getShippingProcessor()->isTrackingInformationForm($trackingNumber)
            : null;
    }

    /**
     * Get the link for the detailed tracking information
     *
     * @param string $trackingNumber Tracking number value
     *
     * @return null|string
     */
    public function getTrackingInformationURL($trackingNumber)
    {
        return $this->getShippingProcessor()
            ? $this->getShippingProcessor()->getTrackingInformationURL($trackingNumber)
            : null;
    }

    /**
     * Get the form parameters for the detailed tracking information
     *
     * @param string $trackingNumber Tracking number value
     *
     * @return null|array
     */
    public function getTrackingInformationParams($trackingNumber)
    {
        return $this->getShippingProcessor()
            ? $this->getShippingProcessor()->getTrackingInformationParams($trackingNumber)
            : null;
    }

    /**
     * Get the form method for the detailed tracking information
     *
     * @param string $trackingNumber Tracking number value
     *
     * @return null|string
     */
    public function getTrackingInformationMethod($trackingNumber)
    {
        return $this->getShippingProcessor()
            ? $this->getShippingProcessor()->getTrackingInformationMethod($trackingNumber)
            : null;
    }

    /**
     * Get first shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getFirstShippingMethod()
    {
        $method = null;

        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        if ($modifier) {
            $rates = $modifier->getRates();
            $rate = array_shift($rates);
            if ($rate) {
                $method = $rate->getMethod();
            }
        }

        return $method;
    }

    // }}}

    // {{{ Mail notification

    /**
     * Set value of isNotificationSent flag and return old value
     *
     * @param boolean $value New value
     *
     * @return boolean
     */
    public function setIsNotificationSent($value)
    {
        $oldValue = $this->isNotificationSent;
        $this->isNotificationSent = $value;

        return $oldValue;
    }

    /**
     * Get value of isNotificationSent flag
     *
     * @return boolean
     */
    public function isNotificationSent()
    {
        return $this->isNotificationSent;
    }

    /**
     * Set value of isNotificationsAllowedFlag flag and return old value
     *
     * @param boolean $value New value
     *
     * @return boolean
     */
    public function setIsNotificationsAllowedFlag($value)
    {
        $oldValue = $this->isNotificationsAllowedFlag;
        $this->isNotificationsAllowedFlag = (boolean)$value;

        return $oldValue;
    }

    /**
     * Set value of ignoreCustomerNotifications flag and return old value
     *
     * @param boolean $value New value
     *
     * @return boolean
     */
    public function setIgnoreCustomerNotifications($value)
    {
        $oldValue = $this->ignoreCustomerNotifications;
        $this->ignoreCustomerNotifications = (boolean)$value;

        return $oldValue;
    }

    /**
     * Get value of isNotificationsAllowedFlag flag
     *
     * @return boolean
     */
    protected function isNotificationsAllowed()
    {
        return $this->isNotificationsAllowedFlag;
    }

    /**
     * Get value of ignoreCustomerNotifications flag
     *
     * @return boolean
     */
    protected function isIgnoreCustomerNotifications()
    {
        return $this->ignoreCustomerNotifications;
    }

    // }}}

    // {{{ Calculation

    /**
     * Get modifiers
     *
     * @return \XLite\DataSet\Collection\OrderModifier
     */
    public function getModifiers()
    {
        if (null === $this->modifiers) {
            $this->modifiers = \XLite\Core\Database::getRepo('XLite\Model\Order\Modifier')->findActive();

            // Initialize
            foreach ($this->modifiers as $modifier) {
                $modifier->initialize($this, $this->modifiers);
            }

            // Preprocess modifiers
            foreach ($this->modifiers as $modifier) {
                $modifier->preprocess();
            }
        }

        return $this->modifiers;
    }

    /**
     * Get modifier
     *
     * @param string $type Modifier type
     * @param string $code Modifier code
     *
     * @return \XLite\Model\Order\Modifier
     */
    public function getModifier($type, $code)
    {
        $result = null;

        foreach ($this->getModifiers() as $modifier) {
            if ($modifier->getType() === $type && $modifier->getCode() === $code) {
                $result = $modifier;
                break;
            }
        }

        return $result;
    }

    /**
     * Check - modifier is exists or not (by type)
     *
     * @param string $type Type
     *
     * @return boolean
     */
    public function isModifierByType($type)
    {
        $result = false;

        foreach ($this->getModifiers() as $modifier) {
            if ($modifier->getType() === $type) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get modifiers by type
     *
     * @param string $type Modifier type
     *
     * @return array
     */
    public function getModifiersByType($type)
    {
        $list = array();

        foreach ($this->getModifiers() as $modifier) {
            if ($modifier->getType() === $type) {
                $list[] = $modifier;
            }
        }

        return $list;
    }

    /**
     * Get items exclude surcharges info
     *
     * @return array
     */
    public function getItemsExcludeSurcharges()
    {
        $list = array();

        foreach ($this->getItems() as $item) {
            foreach ($item->getExcludeSurcharges() as $surcharge) {
                if (!isset($list[$surcharge->getKey()])) {
                    $list[$surcharge->getKey()] = $surcharge->getName();
                }
            }
        }

        return $list;
    }

    /**
     * Get items included surcharges totals
     *
     * @param boolean $forCartItems Flag: true - return values for cart items only, false - for cart totals and items
     *
     * @return array
     */
    public function getItemsIncludeSurchargesTotals($forCartItems = false)
    {
        $list = array();

        if ($forCartItems) {

            // Add surcharges for cart items
            foreach ($this->getItems() as $item) {
                foreach ($item->getIncludeSurcharges() as $surcharge) {
                    if (!isset($list[$surcharge->getKey()])) {
                        $list[$surcharge->getKey()] = array(
                            'surcharge' => $surcharge,
                            'cost'      => 0,
                        );
                    }

                    $list[$surcharge->getKey()]['cost'] += $surcharge->getValue();
                }
            }

        } else {

            // Get global cart surcharges
            foreach ($this->getIncludeSurcharges() as $surcharge) {
                if (!isset($list[$surcharge->getKey()])) {
                    $list[$surcharge->getKey()] = array(
                        'surcharge' => $surcharge,
                        'cost'      => 0,
                    );
                }

                $list[$surcharge->getKey()]['cost'] += $surcharge->getValue();
            }
        }

        return $list;
    }

    /**
     * Common method to update cart/order
     *
     * @return void
     */
    public function updateOrder()
    {
        $this->normalizeItems();

        // If shipping method is not selected or shipping conditions is changed (remove order items or changed address)
        $this->renewShippingMethod();

        $this->calculate();

        $this->renewPaymentMethod();
    }

    /**
     * Calculate order
     *
     * @return void
     */
    public function calculate()
    {
        $oldSurcharges = $this->resetSurcharges();

        $this->reinitializeCurrency();

        $this->calculateInitialValues();

        foreach ($this->getModifiers() as $modifier) {
            if ($modifier->canApply()) {
                $modifier->calculate();
            }
        }

        $this->mergeSurcharges($oldSurcharges);

        $this->finalizeItemsCalculation();

        $this->setTotal(max($this->getSurchargesTotal(), 0));
    }

    /**
     * Recalculate edited order
     *
     * @return void
     */
    public function recalculate()
    {
        $this->reinitializeCurrency();

        $this->finalizeItemsCalculation();

        $this->setTotal($this->getSurchargesTotal());
    }

    /**
     * Renew order
     *
     * @return void
     */
    public function renew()
    {
        foreach ($this->getItems() as $item) {
            if (!$item->renew()) {
                $this->getItems()->removeElement($item);
                \XLite\Core\Database::getRepo('XLite\Model\OrderItem')->delete($item);
            }
        }

        $this->calculate();
    }

    /**
     * Soft renew
     *
     * @return void
     */
    public function renewSoft()
    {
        $this->reinitializeCurrency();
    }

    /**
     * Reinitialize currency
     *
     * @return void
     */
    protected function reinitializeCurrency()
    {
        $new = $this->defineCurrency();
        $old = $this->getCurrency();

        if (empty($old) || (!empty($new) && $old->getCode() !== $new->getCode())) {
            $this->setCurrency($new);
        }
    }

    /**
     * Define order currency
     *
     * @return \XLite\Model\Currency
     */
    protected function defineCurrency()
    {
        return \XLite::getInstance()->getCurrency();
    }

    /**
     * Reset surcharges list
     *
     * @return array
     */
    public function resetSurcharges()
    {
        $result = array(
            'items' => array(),
        );

        $result['items'] = $this->resetItemsSurcharges($this->getItems()->toArray());
        $result['surcharges'] = parent::resetSurcharges();

        return $result;
    }

    /**
     * Reset order items surcharge
     *
     * @param \XLite\Model\OrderItem[] $items Order items
     *
     * @return array
     */
    protected function resetSelfSurcharges($items)
    {
        $result = array();

        foreach ($items as $item) {
            $result[$item->getItemId()] = $item->resetSurcharges();
        }

        return $result;
    }

    /**
     * Reset order items surcharge
     *
     * @param \XLite\Model\OrderItem[] $items Order items
     *
     * @return array
     */
    protected function resetItemsSurcharges($items)
    {
        $result = array();

        foreach ($items as $item) {
            $result[$item->getItemId()] = $item->resetSurcharges();
        }

        return $result;
    }

    /**
     * Merge surcharges
     *
     * @param array $oldSurcharges Old surcharges
     *
     * @return void
     */
    protected function mergeSurcharges(array $oldSurcharges)
    {
        foreach ($this->getItems() as $item) {
            if (!empty($oldSurcharges['items'][$item->getItemId()])) {
                $item->compareSurcharges($oldSurcharges['items'][$item->getItemId()]);
            }
        }

        $this->compareSurcharges($oldSurcharges['surcharges']);
    }

    /**
     * Calculate initial order values
     *
     * @return void
     */
    public function calculateInitialValues()
    {
        $subtotal = 0;

        foreach ($this->getItems() as $item) {
            $item->calculate();

            $subtotal += $item->getSubtotal();
        }

        $subtotal = $this->getCurrency()->roundValue($subtotal);

        $this->setSubtotal($subtotal);
        $this->setTotal($subtotal);
    }

    /**
     * Finalize items calculation
     *
     * @return void
     */
    protected function finalizeItemsCalculation()
    {
        $subtotal = 0;
        foreach ($this->getItems() as $item) {
            $itemTotal = $item->calculateTotal();
            $subtotal += $itemTotal;
            $item->setTotal($itemTotal);
        }

        $this->setSubtotal($subtotal);
        $this->setTotal($subtotal);
    }

    // }}}

    // {{{ Surcharges

    /**
     * Get surcharges by type
     *
     * @param string $type Surcharge type
     *
     * @return array
     */
    public function getSurchargesByType($type = null)
    {
        if ($type) {
            $list = array();
            foreach ($this->getSurcharges() as $surcharge) {
                if ($surcharge->getType() === $type) {
                    $list[] = $surcharge;
                }
            }
        } else {
            $list = $this->getSurcharges();
        }

        return $list;
    }

    /**
     * Get surcharges subtotal with specified type
     *
     * @param string  $type    Surcharge type OPTIONAL
     * @param boolean $include Surcharge include flag OPTIONAL
     *
     * @return float
     */
    public function getSurchargesSubtotal($type = null, $include = null)
    {
        $subtotal = 0;
        $surcharges = $this->getSurchargesByType($type);

        foreach ($surcharges as $surcharge) {
            if ($surcharge->getAvailable() && (null === $include || $surcharge->getInclude() === $include)) {
                $subtotal += $this->getCurrency()->roundValue($surcharge->getValue());
            }
        }

        return $subtotal;
    }

    /**
     * Get subtotal for displaying
     *
     * @return float
     */
    public function getDisplaySubtotal()
    {
        return $this->getSubtotal();
    }

    /**
     * Get surcharges total with specified type
     *
     * @param string $type Surcharge type OPTIONAL
     *
     * @return float
     */
    public function getSurchargesTotal($type = null)
    {
        return $this->getSubtotal() + $this->getSurchargesSubtotal($type, false);
    }

    // }}}

    // {{{ Lifecycle callbacks

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @PrePersist
     * @PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date) || !is_int($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }

        $this->setLastRenewDate(\XLite\Core\Converter::time());
    }

    /**
     * Prepare order before remove operation
     *
     * @return void
     */
    public function prepareBeforeRemove()
    {
        $profile = $this->getProfile();
        $origProfile = $this->getOrigProfile();

        try {
            if ($profile && (!$origProfile || $profile->getProfileId() != $origProfile->getProfileId())) {
                \XLite\Core\Database::getRepo('XLite\Model\Profile')->delete($profile, false);
            }
        } catch (\Doctrine\ORM\EntityNotFoundException $e) {}
    }

    /**
     * Since Doctrine lifecycle callbacks do not allow to modify associations, we've added this method
     *
     * @param string $type Type of current operation
     *
     * @return void
     */
    public function prepareEntityBeforeCommit($type)
    {
        if (static::ACTION_DELETE === $type) {
            $this->setOldPaymentStatus(null);
            $this->setOldShippingStatus(null);

            $this->updateItemsSales(-1);

            $this->prepareBeforeRemove();
        }
    }

    // }}}

    // {{{ Change status routine

    /**
     * Get payment status code
     *
     * @return string
     */
    public function getPaymentStatusCode()
    {
        return $this->getPaymentStatus() && $this->getPaymentStatus()->getCode()
            ? $this->getPaymentStatus()->getCode()
            : '';
    }

    /**
     * Get shipping status code
     *
     * @return string
     */
    public function getShippingStatusCode()
    {
        return $this->getShippingStatus() && $this->getShippingStatus()->getCode()
            ? $this->getShippingStatus()->getCode()
            : '';
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
        $this->processStatus($paymentStatus, 'payment');
    }

    /**
     * Set shipping status
     *
     * @param mixed $shippingStatus Shipping status
     *
     * @return void
     */
    public function setShippingStatus($shippingStatus = null)
    {
        $this->processStatus($shippingStatus, 'shipping');
    }

    /**
     * Process order status
     *
     * @param mixed  $status Status
     * @param string $type   Type
     *
     * @return void
     */
    public function processStatus($status, $type)
    {
        static $cache = array();

        if (is_scalar($status)) {
            if (!isset($cache[$type][$status])) {
                $requestedStatus = $status;

                if (is_int($status)
                    || (is_string($status)
                        && preg_match('/^[\d]+$/', $status)
                    )
                ) {
                    $status = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\\' . ucfirst($type))
                        ->find($status);

                } elseif (is_string($status)) {
                    $status = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\\' . ucfirst($type))
                        ->findOneByCode($status);
                }

                $cache[$type][$requestedStatus] = $status;

            } else {
                $status = $cache[$type][$status];
            }
        }

        $this->statusIsSet = true;

        $this->{'old' . ucfirst($type) . 'Status'} = $this->{$type . 'Status'};
        $this->{$type . 'Status'} = $status;
    }

    /**
     * Check order statuses
     *
     * @return boolean
     *
     * @PostPersist
     * @PostUpdate
     */
    public function checkStatuses()
    {
        $changed = false;

        if ($this->statusIsSet) {
            $this->statusIsSet = false;
            $statusHandlers = array();

            foreach (array('payment', 'shipping') as $type) {
                $status = $this->{$type . 'Status'};
                $oldStatus = $this->{'old' . ucfirst($type) . 'Status'};
                if ($status
                    && $oldStatus
                    && $status->getId() !== $oldStatus->getId()
                ) {
                    \XLite\Core\OrderHistory::getInstance()->registerOrderChangeStatus(
                        $this->getOrderId(),
                        array(
                            'old'  => $oldStatus,
                            'new'  => $status,
                            'type' => $type,
                        )
                    );

                    $statusHandlers = array_merge(
                        $this->getStatusHandlers($oldStatus, $status, $type),
                        $statusHandlers
                    );

                    $changed = true;

                } elseif (!$oldStatus) {
                    $this->{'old' . ucfirst($type) . 'Status'} = $this->{$type . 'Status'};
                }
            }

            if ($statusHandlers) {
                foreach (array_unique($statusHandlers) as $handler) {
                    $this->{'process' . ucfirst($handler)}();
                }
            }

            if ($changed) {
                $this->checkInventory();
            }

            foreach (array('payment', 'shipping') as $type) {
                $this->{'old' . ucfirst($type) . 'Status'} = null;
            }

            if ($changed) {
                if (!$this->isNotificationSent
                    && $this->isNotificationsAllowed()
                    && $this->getOrderNumber()
                ) {
                    \XLite\Core\Mailer::sendOrderChanged($this, $this->isIgnoreCustomerNotifications());
                }
            }
        }

        return $changed;
    }

    /**
     * Check inventory
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @return boolean
     */
    public function checkInventory()
    {
        $property = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Property')->findOneBy(
            array(
                'paymentStatus'  => $this->paymentStatus,
                'shippingStatus' => $this->shippingStatus,
            )
        );

        $incStock = $property ? $property->getIncStock() : null;
        if ($incStock !== null) {
            $property = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Property')->findOneBy(
                array(
                    'paymentStatus'  => $this->oldPaymentStatus,
                    'shippingStatus' => $this->oldShippingStatus,
                )
            );
            $oldIncStock = $property ? $property->getIncStock() : null;

            if (!is_null($oldIncStock) && $incStock !== $oldIncStock) {
                if ($incStock) {
                    $this->processIncrease();

                } else {
                    $this->processDecrease();
                }

                \XLite\Core\Database::getEM()->flush();
            }
        }

        $this->updateSales();
    }

    /**
     * Transform attributes: remove relations between order item attributes and product attribute values
     * to avoid data lost after product attribute values modification
     *
     * @return void
     */
    public function transformItemsAttributes()
    {
        foreach ($this->getItems() as $item) {
            if ($item->hasAttributeValues()) {
                foreach ($item->getAttributeValues() as $av) {
                    if ($av->getAttributeValue()) {
                        $attributeValue = $av->getAttributeValue();
                        $av->setName($attributeValue->getAttribute()->getName());
                        if (!($attributeValue instanceof \XLite\Model\AttributeValue\AttributeValueText)) {
                            $av->setValue($attributeValue->asString());
                        }
                        $av->setAttributeId($attributeValue->getAttribute()->getId());
                    }
                }
            }
        }
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param mixed  $oldStatus  Old order status
     * @param mixed  $newStatus  New order status
     * @param string $type Type
     *
     * @return string|array
     */
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $class = '\XLite\Model\Order\Status\\' . ucfirst($type);

        $oldCode = $oldStatus->getCode();
        $newCode = $newStatus->getCode();
        $statusHandlers = $class::getStatusHandlers();

        $result = [];
        
        if ($oldCode && $newCode && isset($statusHandlers[$oldCode]) && isset($statusHandlers[$oldCode][$newCode])) {
            $result = is_array($statusHandlers[$oldCode][$newCode])
                ? array_unique($statusHandlers[$oldCode][$newCode])
                : $statusHandlers[$oldCode][$newCode];
        }

        return $result;
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processCheckout()
    {
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processDecrease()
    {
        $this->decreaseInventory();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processUncheckout()
    {
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processQueue()
    {
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processAuthorize()
    {
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        if ($this->isNotificationsAllowed()) {
            \XLite\Core\Mailer::sendOrderProcessed($this, $this->isIgnoreCustomerNotifications());
        }

        $this->setIsNotificationSent(true);
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processShip()
    {
        if ($this->isNotificationsAllowed() && !$this->isIgnoreCustomerNotifications()) {
            \XLite\Core\Mailer::sendOrderShipped($this);
        }

        $this->setIsNotificationSent(true);
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processIncrease()
    {
        $this->increaseInventory();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processDecline()
    {
        if ($this->isNotificationsAllowed() && $this->getOrderNumber()) {
            \XLite\Core\Mailer::sendOrderFailed($this, $this->isIgnoreCustomerNotifications());
        }

        $this->setIsNotificationSent(true);
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processFail()
    {
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processCancel()
    {
        if ($this->isNotificationsAllowed()) {
            \XLite\Core\Mailer::sendOrderCanceled($this, $this->isIgnoreCustomerNotifications());
        }

        $this->setIsNotificationSent(true);
    }

    // }}}

    // {{{ Inventory tracking

    /**
     * Increase / decrease item products inventory
     *
     * @param integer $sign Flag; "1" or "-1"
     *
     * @return void
     */
    protected function changeItemsInventory($sign)
    {
        $registerAsGroup = 1 < count($this->getItems());
        $data = array();
        foreach ($this->getItems() as $item) {
            $data[] = $this->getGroupedDataItem($item, $sign * $item->getAmount());
        }

        if ($registerAsGroup) {
            \XLite\Core\OrderHistory::getInstance()->registerChangeAmountGrouped(
                $this->getOrderId(),
                $data
            );
        }

        foreach ($this->getItems() as $item) {
            $amount = $this->changeItemInventory($item, $sign, !$registerAsGroup);
        }
    }

    /**
     * Get grouped data item
     *
     * @param \XLite\Model\OrderItem    $item       Order item
     * @param integer                   $amount     Amount
     *
     * @return array
     */
    protected function getGroupedDataItem($item, $amount)
    {
        return array(
            'item'      => $item,
            'amount'    => $item->getProduct()->getPublicAmount(),
            'delta'     => $amount,
        );
    }

    /**
     * Increase / decrease item product inventory
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param integer                $sign      Flag; "1" or "-1"
     * @param boolean                $register  Register in order history OPTIONAL
     *
     * @return integer
     */
    protected function changeItemInventory($item, $sign, $register = true)
    {
        $history = \XLite\Core\OrderHistory::getInstance();
        $amount = $sign * $item->getAmount();

        if ($register) {
            $history->registerChangeAmount($this->getOrderId(), $item->getProduct(), $amount);
        }
        $item->changeAmount($amount);

        return $amount;
    }

    /**
     * Order processed: decrease products inventory
     *
     * @return void
     */
    protected function decreaseInventory()
    {
        $this->changeItemsInventory(-1);
    }

    /**
     * Order declined: increase products inventory
     *
     * @return void
     */
    protected function increaseInventory()
    {
        $this->changeItemsInventory(1);
    }

    // }}}

    // {{{ Order actions

    /**
     * Get allowed actions
     *
     * @return array
     */
    public function getAllowedActions()
    {
        return array();
    }

    /**
     * Get allowed payment actions
     *
     * @return array
     */
    public function getAllowedPaymentActions()
    {
        $actions = array();

        $transactions = $this->getPaymentTransactions();

        if ($transactions) {
            foreach ($transactions as $transaction) {
                $processor = $transaction->getPaymentMethod()
                    ? $transaction->getPaymentMethod()->getProcessor()
                    : null;

                if ($processor) {
                    $allowedTransactions = $processor->getAllowedTransactions();

                    foreach ($allowedTransactions as $transactionType) {
                        if ($processor->isTransactionAllowed($transaction, $transactionType)) {
                            $actions[$transactionType] = $transaction->getTransactionId();
                        }
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * Get array of payment transaction sums (how much is authorized, captured and refunded)
     *
     * @return array
     */
    public function getPaymentTransactionSums()
    {
        $paymentTransactionSums = $this->getRawPaymentTransactionSums();

        $paymentTransactionSums = array(
            static::t('Authorized amount') => $paymentTransactionSums['authorized'],
            static::t('Captured amount')   => $paymentTransactionSums['captured'],
            static::t('Refunded amount')   => $paymentTransactionSums['refunded'],
        );

        // Remove from array all zero sums
        foreach ($paymentTransactionSums as $k => $v) {
            if (0.01 >= $v) {
                unset($paymentTransactionSums[$k]);
            }
        }

        return $paymentTransactionSums;
    }

    /**
     * Get array of raw payment transaction sums
     *
     * @param boolean $override Override cache OPTIONAL
     *
     * @return array
     */
    public function getRawPaymentTransactionSums($override = false)
    {
        if (null === $this->paymentTransactionSums || $override) {
            $transactions = $this->getPaymentTransactions();

            $this->paymentTransactionSums = array(
                'authorized' => 0,
                'captured'   => 0,
                'refunded'   => 0,
                'sale'       => 0,
                'blocked'    => 0,
            );

            foreach ($transactions as $t) {
                if ($t->isPersistent()) {
                    \XLite\Core\Database::getEM()->refresh($t);
                }
                $backendTransactions = $t->getBackendTransactions();

                $authorized = 0;

                if ($backendTransactions && count($backendTransactions) > 0) {
                    // By backend transactions
                    foreach ($backendTransactions as $bt) {
                        if ($bt->isCompleted()) {
                            switch ($bt->getType()) {
                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH:
                                    $authorized += $bt->getValue();
                                    $this->paymentTransactionSums['blocked'] += $bt->getValue();
                                    break;

                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE:
                                    $this->paymentTransactionSums['blocked'] += $bt->getValue();
                                    $this->paymentTransactionSums['sale'] += $bt->getValue();
                                    break;

                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE:
                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART:
                                    $this->paymentTransactionSums['captured'] += $bt->getValue();
                                    $authorized -= $bt->getValue();
                                    $this->paymentTransactionSums['sale'] += $bt->getValue();
                                    break;

                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND:
                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART:
                                    $this->paymentTransactionSums['refunded'] += $bt->getValue();
                                    $this->paymentTransactionSums['blocked'] -= $bt->getValue();
                                    $this->paymentTransactionSums['sale'] -= $bt->getValue();
                                    break;

                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI:
                                    $this->paymentTransactionSums['refunded'] += $bt->getValue();
                                    $authorized -= $bt->getValue();
                                    $this->paymentTransactionSums['blocked'] -= $bt->getValue();
                                    $this->paymentTransactionSums['sale'] -= $bt->getValue();
                                    break;

                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID:
                                case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID_PART:
                                    $authorized -= $bt->getValue();
                                    $this->paymentTransactionSums['blocked'] -= $bt->getValue();
                                    break;

                                default:
                            }
                        }
                    }

                } else {
                    // By transaction
                    if ($t->isCompleted()) {
                        switch ($t->getType()) {
                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH:
                                $authorized += $t->getValue();
                                $this->paymentTransactionSums['blocked'] += $t->getValue();
                                break;

                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE:
                                $this->paymentTransactionSums['blocked'] += $t->getValue();
                                $this->paymentTransactionSums['sale'] += $t->getValue();
                                break;

                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE:
                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART:
                                $this->paymentTransactionSums['captured'] += $t->getValue();
                                $authorized -= $t->getValue();
                                $this->paymentTransactionSums['sale'] += $t->getValue();
                                break;

                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND:
                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART:
                                $this->paymentTransactionSums['refunded'] += $t->getValue();
                                $this->paymentTransactionSums['blocked'] -= $t->getValue();
                                $this->paymentTransactionSums['sale'] -= $t->getValue();
                                break;

                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI:
                                $this->paymentTransactionSums['refunded'] += $t->getValue();
                                $authorized -= $t->getValue();
                                $this->paymentTransactionSums['blocked'] -= $t->getValue();
                                $this->paymentTransactionSums['sale'] -= $t->getValue();
                                break;

                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID:
                            case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID_PART:
                                $authorized -= $t->getValue();
                                $this->paymentTransactionSums['blocked'] -= $t->getValue();
                                break;

                            default:
                        }
                    }
                }

                if (0 < $authorized
                    && 0 < $this->paymentTransactionSums['captured']
                    && !$t->getPaymentMethod()->getProcessor()->isTransactionAllowed($t, \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_MULTI)
                ) {
                    // Do not take in consideration an authorized sum after capture
                    // if payment processor does not support multiple partial capture transactions
                    $authorized = 0;
                }

                $this->paymentTransactionSums['authorized'] += $authorized;

            } // foreach
        }

        return $this->paymentTransactionSums;
    }

    // }}}

    // {{{ Common for several pages method to use in invoice templates

    /**
     * Return true if shipping section should be visible on the invoice
     *
     * @return boolean
     */
    public function isShippingSectionVisible()
    {
        $modifier = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $modifier && $modifier->canApply();
    }

    /**
     * Return true if payment section should be visible on the invoice
     * (this section is always visible by the default)
     *
     * @return boolean
     */
    public function isPaymentSectionVisible()
    {
        return true;
    }

    /**
     * Return true if payment and/or shipping sections should be visible on the invoice
     *
     * @return boolean
     */
    public function isPaymentShippingSectionVisible()
    {
        return $this->isShippingSectionVisible() || $this->isPaymentSectionVisible();
    }

    // }}}

    /**
     * Renew payment status.
     * Return true if payment status has been changed
     *
     * @return boolean
     */
    public function renewPaymentStatus()
    {
        $result = false;

        $status = $this->getCalculatedPaymentStatus(true);
        if ($this->getPaymentStatusCode() !== $status) {
            $this->setPaymentStatus($status);
            $result = true;
        }

        return $result;
    }

    /**
     * Get calculated payment status
     *
     * @param boolean $override Override calculation cache OPTIONAL
     *
     * @return string
     */
    public function getCalculatedPaymentStatus($override = false)
    {
        $result = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;

        $sums = $this->getRawPaymentTransactionSums($override);
        $total = $this->getCurrency()->roundValue($this->getTotal());

        if (0.0 == $total) {
            $result = \XLite\Model\Order\Status\Payment::STATUS_PAID;

        } elseif ($sums['authorized'] > 0 && $sums['sale'] == 0 && $sums['captured'] == 0) {
            $result = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;

        } elseif ($sums['sale'] < $total) {
            if ($sums['sale'] > 0) {
                $result = \XLite\Model\Order\Status\Payment::STATUS_PART_PAID;

            } elseif ($sums['refunded'] > 0) {
                $result = \XLite\Model\Order\Status\Payment::STATUS_REFUNDED;
            }

        } else {
            if ($sums['sale'] > 0 || $sums['captured'] > 0) {
                $result = \XLite\Model\Order\Status\Payment::STATUS_PAID;

            } elseif ($sums['refunded'] > 0) {
                $result = \XLite\Model\Order\Status\Payment::STATUS_REFUNDED;
            }

        }

        if (\XLite\Model\Order\Status\Payment::STATUS_QUEUED === $result) {
            $lastTransaction = $this->getPaymentTransactions()->last();
            if ($lastTransaction) {
                if ($lastTransaction->isFailed() || $lastTransaction->isVoid()) {
                    $result = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;
                }

                if ($lastTransaction->isCanceled()) {
                    $result = \XLite\Model\Order\Status\Payment::STATUS_CANCELED;
                }
            }
        }

        return $result;
    }

    /**
     * Set order status by transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction which changes status
     *
     * @return void
     */
    public function setPaymentStatusByTransaction(\XLite\Model\Payment\Transaction $transaction)
    {
        if ($this->isPayed()) {
            $status = $transaction->isCaptured()
                ? \XLite\Model\Order\Status\Payment::STATUS_PAID
                : \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
        } else {
            if ($transaction->isRefunded()) {
                $paymentTransactionSums = $this->getRawPaymentTransactionSums(true);
                $refunded = $paymentTransactionSums['refunded'];

                // Check if the whole refunded sum (along with the previous refunded transactions for the order)
                // covers the whole total for order
                $status = $this->getCurrency()->roundValue($refunded) < $this->getCurrency()->roundValue($this->getTotal())
                    ? \XLite\Model\Order\Status\Payment::STATUS_PART_PAID
                    : \XLite\Model\Order\Status\Payment::STATUS_REFUNDED;

            } elseif ($transaction->isFailed()) {
                $status = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;

            } elseif ($transaction->isVoid()) {
                $status = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;

            } elseif ($transaction->isCaptured()) {
                $status = \XLite\Model\Order\Status\Payment::STATUS_PART_PAID;
            } else {
                $status = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
            }
        }

        $this->setPaymentStatus($status);
    }

    /**
     * Checks whether order is shippable or not
     *
     * @return boolean
     */
    public function isShippable()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->isShippable()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Get addresses list
     *
     * @return array
     */
    public function getAddresses()
    {
        $list = $this->getProfile()->getAddresses()->toArray();
        if ($this->getOrigProfile()) {
            foreach ($this->getOrigProfile()->getAddresses() as $address) {
                $equal = false;
                foreach ($list as $address2) {
                    if (!$equal && $address->isEqualAddress($address2)) {
                        $equal = true;
                        break;
                    }
                }

                if (!$equal) {
                    $list[] = $address;
                }
            }
        }

        return $list;
    }

    /**
     * Check if all order items in order is configured
     *
     * @return boolean
     */
    protected function isConfigured()
    {
        $isConfigured = true;

        foreach ($this->getItems() as $item) {
            if (!$item->isConfigured()) {
                $isConfigured = false;
                break;
            }
        }

        return $isConfigured;
    }

    /**
     * Get payment transaction data.
     * These data are displayed on the order page, invoice and packing slip
     *
     * @param boolean $isPrimary Flag: true - return only data fields marked by processor as 'primary', false - all fields OPTIONAL
     *
     * @return array
     */
    public function getPaymentTransactionData($isPrimary = false)
    {
        $result = array();

        $transaction = $this->getPaymentTransactions()
            ? $this->getPaymentTransactions()->last()
            : null;

        if ($transaction) {
            $method = $transaction->getPaymentMethod() ?: $this->getPaymentMethod();
            $processor = $method ? $method->getProcessor() : null;

            if ($processor) {
                $result = $processor->getTransactionData($transaction, $isPrimary);
            }
        }

        return $result;
    }

    /**
     * Get last payment transaction ID.
     * This data is displayed on the order page, invoice and packing slip
     *
     * @return string|null
     */
    public function getPaymentTransactionId()
    {
        $transaction = $this->getPaymentTransactions()
            ? $this->getPaymentTransactions()->last()
            : null;

        return $transaction
            ? $transaction->getPublicId()
            : null;
    }

    /**
     * @return bool
     */
    public function isOfflineProcessorUsed()
    {
        $processor = $this->getPaymentProcessor();

        return $processor instanceof \XLite\Model\Payment\Processor\Offline;
    }

    /**
     * @return \XLite\Model\Payment\Base\Processor
     */
    public function getPaymentProcessor()
    {
        $processor = null;

        $transaction = $this->getPaymentTransactions()
            ? $this->getPaymentTransactions()->last()
            : null;

        if ($transaction) {
            $method = $transaction->getPaymentMethod() ?: $this->getPaymentMethod();
            $processor = $method ? $method->getProcessor() : null;
        }

        return $processor;
    }

    // {{{ Sales statistic

    /**
     * Returns old payment status code
     *
     * @return string
     */
    protected function getOldPaymentStatusCode()
    {
        $oldPaymentStatus = $this->oldPaymentStatus;

        return $oldPaymentStatus && $oldPaymentStatus->getCode()
            ? $oldPaymentStatus->getCode()
            : '';
    }

    /**
     * Calculate sales delta
     *
     * @return integer|null
     */
    protected function getSalesDelta()
    {
        $result = null;

        $newStatusCode = $this->getPaymentStatusCode();
        if ($newStatusCode) {
            $oldStatusCode = $this->getOldPaymentStatusCode();
            $paidStatuses = \XLite\Model\Order\Status\Payment::getPaidStatuses();

            if ((!$oldStatusCode || !in_array($oldStatusCode, $paidStatuses, true))
                && in_array($newStatusCode, $paidStatuses, true)
            ) {
                $result = 1;

            } elseif ($oldStatusCode
                && in_array($oldStatusCode, $paidStatuses, true)
                && !in_array($newStatusCode, $paidStatuses, true)
            ) {
                $result = -1;
            }
        }

        return $result;
    }

    /**
     * Update sales statistics
     *
     * @param integer $delta Delta
     */
    protected function updateItemsSales($delta)
    {
        if (null === $delta) {
            return;
        }

        foreach ($this->getItems() as $item) {
            $product = $item->getObject();
            if (null === $product) {
                continue;
            }

            $product->setSales(
                $product->getSales() + $delta * $item->getAmount()
            );
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Update sales
     */
    protected function updateSales()
    {
        $this->updateItemsSales($this->getSalesDelta());
    }

    // }}}

    /**
     * Get order_id
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set shipping_id
     *
     * @param integer $shippingId
     * @return Order
     */
    public function setShippingId($shippingId)
    {
        $this->shipping_id = $shippingId;
        return $this;
    }

    /**
     * Get shipping_id
     *
     * @return integer 
     */
    public function getShippingId()
    {
        return $this->shipping_id;
    }

    /**
     * Set shipping_method_name
     *
     * @param string $shippingMethodName
     * @return Order
     */
    public function setShippingMethodName($shippingMethodName)
    {
        $this->shipping_method_name = $shippingMethodName;
        return $this;
    }

    /**
     * Set payment_method_name
     *
     * @param string $paymentMethodName
     * @return Order
     */
    public function setPaymentMethodName($paymentMethodName)
    {
        $this->payment_method_name = $paymentMethodName;
        return $this;
    }

    /**
     * Set tracking
     *
     * @param string $tracking
     * @return Order
     */
    public function setTracking($tracking)
    {
        $this->tracking = $tracking;
        return $this;
    }

    /**
     * Get tracking
     *
     * @return string 
     */
    public function getTracking()
    {
        return $this->tracking;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Order
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set lastRenewDate
     *
     * @param integer $lastRenewDate
     * @return Order
     */
    public function setLastRenewDate($lastRenewDate)
    {
        $this->lastRenewDate = $lastRenewDate;
        return $this;
    }

    /**
     * Get lastRenewDate
     *
     * @return integer 
     */
    public function getLastRenewDate()
    {
        return $this->lastRenewDate;
    }

    /**
     * Set notes
     *
     * @param text $notes
     * @return Order
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * Get notes
     *
     * @return text 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set adminNotes
     *
     * @param text $adminNotes
     * @return Order
     */
    public function setAdminNotes($adminNotes)
    {
        $this->adminNotes = $adminNotes;
        return $this;
    }

    /**
     * Get adminNotes
     *
     * @return text 
     */
    public function getAdminNotes()
    {
        return $this->adminNotes;
    }

    /**
     * Set orderNumber
     *
     * @param string $orderNumber
     * @return Order
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Set recent
     *
     * @param boolean $recent
     * @return Order
     */
    public function setRecent($recent)
    {
        $this->recent = $recent;
        return $this;
    }

    /**
     * Get recent
     *
     * @return boolean 
     */
    public function getRecent()
    {
        return $this->recent;
    }

    /**
     * Set xcPendingExport
     *
     * @param boolean $xcPendingExport
     * @return Order
     */
    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
        return $this;
    }

    /**
     * Get xcPendingExport
     *
     * @return boolean 
     */
    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile 
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Get orig_profile
     *
     * @return \XLite\Model\Profile 
     */
    public function getOrigProfile()
    {
        return $this->orig_profile;
    }

    /**
     * Get paymentStatus
     *
     * @return \XLite\Model\Order\Status\Payment 
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Get shippingStatus
     *
     * @return \XLite\Model\Order\Status\Shipping 
     */
    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }

    /**
     * Add details
     *
     * @param \XLite\Model\OrderDetail $details
     * @return Order
     */
    public function addDetails(\XLite\Model\OrderDetail $details)
    {
        $this->details[] = $details;
        return $this;
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection|\XLite\Model\OrderDetail[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Add trackingNumbers
     *
     * @param \XLite\Model\OrderTrackingNumber $trackingNumbers
     * @return Order
     */
    public function addTrackingNumbers(\XLite\Model\OrderTrackingNumber $trackingNumbers)
    {
        $this->trackingNumbers[] = $trackingNumbers;
        return $this;
    }

    /**
     * Get trackingNumbers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrackingNumbers()
    {
        return $this->trackingNumbers;
    }

    /**
     * Add events
     *
     * @param \XLite\Model\OrderHistoryEvents $events
     * @return Order
     */
    public function addEvents(\XLite\Model\OrderHistoryEvents $events)
    {
        $this->events[] = $events;
        return $this;
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add items
     *
     * @param \XLite\Model\OrderItem $items
     * @return Order
     */
    public function addItems(\XLite\Model\OrderItem $items)
    {
        $this->items[] = $items;
        return $this;
    }

    /**
     * Get items
     *
     * @return \XLite\Model\OrderItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add surcharges
     *
     * @param \XLite\Model\Order\Surcharge $surcharges
     * @return Order
     */
    public function addSurcharges(\XLite\Model\Order\Surcharge $surcharges)
    {
        $this->surcharges[] = $surcharges;
        return $this;
    }

    /**
     * Get surcharges
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSurcharges()
    {
        return $this->surcharges;
    }

    /**
     * Add payment_transactions
     *
     * @param \XLite\Model\Payment\Transaction $paymentTransactions
     * @return Order
     */
    public function addPaymentTransactions(\XLite\Model\Payment\Transaction $paymentTransactions)
    {
        $this->payment_transactions[] = $paymentTransactions;
        return $this;
    }

    /**
     * Get payment_transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPaymentTransactions()
    {
        return $this->payment_transactions;
    }

    /**
     * Set currency
     *
     * @param \XLite\Model\Currency $currency
     * @return Order
     */
    public function setCurrency(\XLite\Model\Currency $currency = null)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
