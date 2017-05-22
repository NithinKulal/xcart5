<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

/**
 * Payment transaction
 *
 * @Entity
 * @Table  (name="payment_transactions",
 *      indexes={
 *          @Index (name="status", columns={"status"}),
 *          @Index (name="o", columns={"order_id","status"}),
 *          @Index (name="pm", columns={"method_id","status"}),
 *          @Index (name="publicTxnId", columns={"publicTxnId"})
 *      }
 * )
 * @HasLifecycleCallbacks
 */
class Transaction extends \XLite\Model\AEntity
{
    /**
     * Transaction status codes
     */

    const STATUS_INITIALIZED = 'I';
    const STATUS_INPROGRESS  = 'P';
    const STATUS_SUCCESS     = 'S';
    const STATUS_PENDING     = 'W';
    const STATUS_FAILED      = 'F';
    const STATUS_CANCELED    = 'C';
    const STATUS_VOID        = 'V';

    /**
     * Transaction initialization result
     */

    const PROLONGATION = 'R';
    const COMPLETED    = 'C';
    const SILENT       = 'S';
    const SEPARATE     = 'E';


    /**
     * Public token length
     */
    const SUFFIX_TOKEN_LENGTH = 4;
    const ORDERID_TOKEN_LENGTH = 6;

    /**
     * Token characters list
     *
     * @var array
     */
    protected static $chars = array(
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z',
    );

    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $transaction_id;

    /**
     * Transaction creation timestamp
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $date;

    /**
     * Public transaction id
     *
     * @var string
     *
     * @Column (type="string", length=16, nullable=true)
     */
    protected $publicTxnId;

    /**
     * Payment method name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $method_name;

    /**
     * Payment method localized name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $method_local_name = '';

    /**
     * Status
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_INITIALIZED;

    /**
     * Transaction value
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Customer message
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $note = '';

    /**
     * Transaction type
     *
     * @var string
     *
     * @Column (type="string", length=16)
     */
    protected $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;

    /**
     * Public transaction ID
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $public_id = '';

    /**
     * Order
     *
     * @var \XLite\Model\Order
     *
     * @ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="payment_transactions")
     * @JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Payment method
     *
     * @var \XLite\Model\Payment\Method
     *
     * @ManyToOne  (targetEntity="XLite\Model\Payment\Method")
     * @JoinColumn (name="method_id", referencedColumnName="method_id", onDelete="SET NULL")
     */
    protected $payment_method;

    /**
     * Transaction data
     *
     * @var \XLite\Model\Payment\TransactionData
     *
     * @OneToMany (targetEntity="XLite\Model\Payment\TransactionData", mappedBy="transaction", cascade={"all"})
     */
    protected $data;

    /**
     * Related backend transactions
     *
     * @var \XLite\Model\Payment\BackendTransaction
     *
     * @OneToMany (targetEntity="XLite\Model\Payment\BackendTransaction", mappedBy="payment_transaction", cascade={"all"})
     */
    protected $backend_transactions;

    /**
     * Currency
     *
     * @var \XLite\Model\Currency
     *
     * @ManyToOne  (targetEntity="XLite\Model\Currency", cascade={"merge","detach"})
     * @JoinColumn (name="currency_id", referencedColumnName="currency_id")
     */
    protected $currency;

    /**
     * Readable statuses
     *
     * @var array
     */
    protected $readableStatuses = array(
        self::STATUS_INITIALIZED => 'Initialized',
        self::STATUS_INPROGRESS  => 'In progress',
        self::STATUS_SUCCESS     => 'Completed',
        self::STATUS_PENDING     => 'Pending',
        self::STATUS_FAILED      => 'Failed',
        self::STATUS_CANCELED    => 'Canceled',
        self::STATUS_VOID        => 'Voided',
    );

    /**
     * Run-time cache of registered transaction data
     *
     * @var array
     */
    protected $registeredCache;

    /**
     * @return bool
     */
    public static function showInitializedTransactions()
    {
        return (bool) \XLite::getInstance()->getOptions(array('other', 'show_initialized_transactions'));
    }

    /**
     * Get statuses
     *
     * @return array
     */
    public static function getStatuses()
    {
        return array(
            static::STATUS_INITIALIZED => 'Initialized',
            static::STATUS_INPROGRESS  => 'In progress',
            static::STATUS_SUCCESS     => 'Success',
            static::STATUS_PENDING     => 'Pending',
            static::STATUS_FAILED      => 'Failed',
            static::STATUS_CANCELED    => 'Canceled',
            static::STATUS_VOID        => 'Void',
        );
    }

    /**
     * Returns default failed reason
     *
     * @return string
     */
    public static function getDefaultFailedReason()
    {
        return static::t('An error occurred, please try again. If the problem persists, contact the administrator.');
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->getOrder() ? $this->getOrder()->getProfile() : null;
    }

    /**
     * Get original profile
     *
     * @return \XLite\Model\Profile
     */
    public function getOrigProfile()
    {
        $profile = $this->getOrder() ? $this->getOrder()->getOrigProfile() : null;

        return $profile ?: $this->getProfile();
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (!$this->getPublicTxnId()) {
            $this->setPublicTxnId(
                $this->generateTransactionId()
            );
        }

        if (!$this->getDate()) {
            $this->setDate(\XLite\Core\Converter::time());
        }

        if (!$this->getPublicId()) {
            if ($this->getPaymentMethod()) {
                $this->renewTransactionId();

            } else {
                $this->setPublicId($this->getPublicTxnId());
            }
        }
    }

    /**
     * Generate new transaction id by format: "OrderId-XXXX", where XXXX is random suffix
     *
     * @return string
     */
    public function generateTransactionId()
    {
        $suffix = \XLite\Core\Operator::getInstance()->generateToken(static::SUFFIX_TOKEN_LENGTH, static::$chars);

        return $this->getOrder()
            ? str_pad($this->getOrder()->getOrderId(), static::ORDERID_TOKEN_LENGTH, '0', STR_PAD_LEFT) . '-' . $suffix
            : null;
    }

    /**
     * Renew transaction ID
     *
     * @return void
     */
    public function renewTransactionId()
    {
        if (!$this->getPublicTxnId()) {
            $this->setPublicTxnId(
                $this->generateTransactionId()
            );
        }

        if ($this->getPaymentMethod() && $this->getPaymentMethod()->getProcessor()) {
            $this->setPublicId(
                $this->getPaymentMethod()->getProcessor()->generateTransactionId($this)
            );
        }
    }

    /**
     * Set transaction value
     *
     * @param float $value Transaction value
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function setValue($value)
    {
        $this->value = $this->getOrder() ? $this->getOrder()->getCurrency()->roundValue($value) : $value;

        return $this;
    }

    /**
     * Set payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method OPTIONAL
     *
     * @return void
     */
    public function setPaymentMethod(\XLite\Model\Payment\Method $method = null)
    {
        $this->payment_method = $method;

        if ($method) {
            $this->setMethodName($method->getServiceName());
            $this->setMethodLocalName($method->getName());
            $this->renewTransactionId();
        }
    }

    /**
     * Update value
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function updateValue(\XLite\Model\Order $order)
    {
        return $this->setValue($order->getOpenTotal());
    }

    /**
     * Process checkout action
     *
     * @return string
     */
    public function handleCheckoutAction()
    {
        $this->setStatus(self::STATUS_INPROGRESS);
        \XLite\Core\Database::getEM()->flush();

        $data = is_array(\XLite\Core\Request::getInstance()->payment)
            ? \XLite\Core\Request::getInstance()->payment
            : array();

        $result = $this->getPaymentMethod()->getProcessor()->pay($this, $data);

        $return = self::COMPLETED;

        switch ($result) {
            case \XLite\Model\Payment\Base\Processor::PROLONGATION:
                $return = self::PROLONGATION;
                break;

            case \XLite\Model\Payment\Base\Processor::SILENT:
                $return = self::SILENT;
                break;

            case \XLite\Model\Payment\Base\Processor::SEPARATE:
                $return = self::SEPARATE;
                break;

            case \XLite\Model\Payment\Base\Processor::COMPLETED:
                $this->setStatus(self::STATUS_SUCCESS);
                break;

            case \XLite\Model\Payment\Base\Processor::PENDING:
                $this->setStatus(self::STATUS_PENDING);
                break;

            default:
                $this->setStatus(self::STATUS_FAILED);
        }

        $this->registerTransactionInOrderHistory();

        return $return;
    }

    /**
     * Get charge value modifier
     *
     * @return float
     */
    public function getChargeValueModifier()
    {
        $value = 0;
        $valueCaptured = 0;
        $valueRefunded = 0;

        if ($this->isCompleted() || $this->isPending()) {
            $value += $this->getValue();
        }

        if ($this->getBackendTransactions()) {
            /** @var \XLite\Model\Payment\BackendTransaction $transaction */
            foreach ($this->getBackendTransactions() as $transaction) {
                if ($transaction->isCapture() && $transaction->isSucceed()) {;
                    $valueCaptured += abs($transaction->getValue());
                }

                if ($transaction->isRefund() && $transaction->isSucceed()) {
                    $valueRefunded += abs($transaction->getValue());
                }
            }
        }

        return max(
            0,
            max($valueCaptured, $value) - $valueRefunded
        );
    }

    /**
     * Check - transaction is open or not
     *
     * @return boolean
     */
    public function isOpen()
    {
        return static::STATUS_INITIALIZED == $this->getStatus();
    }

    /**
     * Check - transaction is canceled or not
     *
     * @return boolean
     */
    public function isCanceled()
    {
        return static::STATUS_CANCELED == $this->getStatus();
    }

    /**
     * Check - transaction is failed or not
     *
     * @return boolean
     */
    public function isFailed()
    {
        return static::STATUS_FAILED == $this->getStatus();
    }

    /**
     * Check - order is completed or not
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return static::STATUS_SUCCESS == $this->getStatus();
    }

    /**
     * Check - order is in progress state or not
     *
     * @return boolean
     */
    public function isInProgress()
    {
        return static::STATUS_INPROGRESS == $this->getStatus();
    }

    /**
     * Return true if transaction is in PENDING status
     *
     * @return boolean
     */
    public function isPending()
    {
        return static::STATUS_PENDING == $this->getStatus();
    }

    /**
     * Return true if transaction is in VOID status
     *
     * @return boolean
     */
    public function isVoid()
    {
        return static::STATUS_VOID == $this->getStatus();
    }

    /**
     * Returns true if successful payment has type AUTH
     *
     * @return boolean
     */
    public function isAuthorized()
    {
        $result = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH == $this->getType() && $this->isCompleted();

        if ($result && $this->getBackendTransactions()) {
            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isVoid()
                    && $transaction->isSucceed()
                ) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true if successful payment has type SALE or has successful CAPTURE transaction
     *
     * @return boolean
     */
    public function isCaptured()
    {
        $result = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE == $this->getType() && $this->isCompleted();

        if ($this->getBackendTransactions()) {

            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isCapture()
                    && $transaction->isSucceed()
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true if payment has successful REFUND transaction
     *
     * @return boolean
     */
    public function isRefunded()
    {
        $result = false;

        if ($this->getBackendTransactions()) {
            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isRefund()
                    && $transaction->isSucceed()
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true if payment has successful REFUND transaction
     *
     * @return boolean
     */
    public function isRefundedNotMulti()
    {
        $result = false;

        if ($this->getBackendTransactions()) {
            foreach ($this->getBackendTransactions() as $transaction) {
                if (
                    $transaction->isRefund() 
                    && $transaction->getType() !== \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI
                    && $transaction->isSucceed()
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Returns true if CAPTURE transaction is allowed for this payment
     *
     * @return boolean
     */
    public function isCaptureTransactionAllowed()
    {
        return $this->isAuthorized() && !$this->isCaptured() && !$this->isRefunded();
    }

    /**
     * Returns true if VOID transaction is allowed for this payment
     *
     * @return boolean
     */
    public function isVoidTransactionAllowed()
    {
        return $this->isCaptureTransactionAllowed();
    }

    /**
     * Returns true if REFUND transaction is allowed for this payment
     *
     * @return boolean
     */
    public function isRefundTransactionAllowed()
    {
        return $this->isCaptured() && !$this->isRefunded();
    }

    /**
     * Returns true if REFUND PART transaction is allowed for this payment
     *
     * @return boolean
     */
    public function isRefundPartTransactionAllowed()
    {
        return $this->isCaptured() && !$this->isRefunded();
    }

    /**
     * Returns true if REFUND MULTIPLE transaction is allowed for this payment
     *
     * @return boolean
     */
    public function isRefundMultiTransactionAllowed()
    {
        $currency = $this->getCurrency() ?: $this->getOrder()->getCurrency();
        return $this->isCaptured() && $currency->roundValue($this->getChargeValueModifier()) > 0;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = array())
    {
        $this->data = new \Doctrine\Common\Collections\ArrayCollection();
        $this->backend_transactions = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get human-readable status
     *
     * @param string $status Transaction status
     *
     * @return string
     */
    public function getReadableStatus($status = null)
    {
        if (!isset($status)) {
            $status = $this->getStatus();
        }

        return isset($this->readableStatuses[$status])
            ? $this->readableStatuses[$status]
            : 'Unknown (' . $status . ')';
    }

    // {{{ Data operations

    /**
     * Get list of transaction data matched to the data list defined in processor
     * Return processor-specific data or (of it is empty and not strict mode) all stored data
     *
     * @param boolean $strict Strict flag
     *
     * @return array
     */
    public function getTransactionData($strict = false)
    {
        $list = new \Doctrine\Common\Collections\ArrayCollection();

        $inputParams = $this->getPaymentMethod() && $this->getPaymentMethod()->getProcessor()
            ? $this->getPaymentMethod()->getProcessor()->getInputDataFields()
            : array();

        if ($inputParams) {
            foreach ($this->getData() as $cell) {
                if (isset($inputParams[$cell->getName()])) {
                    $list->add($cell);
                    unset($inputParams[$cell->getName()]);
                }
            }

            if ($inputParams && $strict) {
                foreach ($inputParams as $param => $paramData) {
                    $cell = new \XLite\Model\Payment\TransactionData();
                    $cell->setName($param);
                    $cell->setLabel($paramData['label']);
                    $cell->setAccessLevel($paramData['accessLevel']);
                    $cell->setTransaction($this);
                    $list->add($cell);
                }
            }
        }

        return $list->isEmpty() && !$strict ? $this->getData() : $list;
    }

    /**
     * Set data cell
     *
     * @param string $name  Data cell name
     * @param string $value Value
     * @param string $label Public name OPTIONAL
     * @param string $accessLevel access level OPTIONAL
     *
     * @return void
     */
    public function setDataCell($name, $value, $label = null, $accessLevel = null)
    {
        $data = null;

        if (!isset($value)) {
            $value = '';
        }

        foreach ($this->getData() as $cell) {
            if ($cell->getName() == $name) {
                $data = $cell;
                break;
            }
        }

        if (!$data) {
            $data = new \XLite\Model\Payment\TransactionData;
            $data->setName($name);
            $this->addData($data);
            $data->setTransaction($this);
        }

        if (!$data->getLabel() && $label) {
            $data->setLabel($label);
        }

        $data->setValue($value);

        // If access level was specified, and it dosn't match original one
        // Then update it
        if (
            $accessLevel
            && $data->getAccessLevel() != $accessLevel
        ) {
            $data->setAccessLevel($accessLevel);
        }
    }

    /**
     * Get data cell object by name
     *
     * @param string $name Name of data cell
     *
     * @return \XLite\Model\Payment\TransactionData
     */
    public function getDataCell($name)
    {
        $value = null;

        foreach ($this->getData() as $cell) {
            if ($cell->getName() == $name) {
                if (
                    \XLite::isAdminZone()
                    || (!\XLite::isAdminZone() && $cell->getAccessLevel() != 'A')
                ) {
                    $value = $cell;
                    break;
                }
            }
        }

        // TODO: Consider situations if cells with same names have different access levels

        return $value;
    }

    /**
     * Get data cell object by name
     *
     * @param string $name Name of data cell
     *
     * @return mixed
     */
    public function getDetail($name)
    {
        $value = $this->getDataCell($name);

        return $value ? $value->getValue() : null;
    }

    // }}}

    /**
     * Create backend transaction
     *
     * @param string $transactionType Type of backend transaction
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function createBackendTransaction($transactionType)
    {
        $bt = \XLite\Core\Database::getRepo('XLite\Model\Payment\BackendTransaction')->insert(
            $this->getCreateBackendTransactionData($transactionType),
            false
        );

        $this->addBackendTransactions($bt);

        return $bt;
    }

    /**
     * Get data to create backend transaction
     *
     * @param string $transactionType Type of backend transaction
     *
     * @return array
     */
    protected function getCreateBackendTransactionData($transactionType)
    {
        $data = array(
            'date'                => \XLite\Core\Converter::time(),
            'type'                => $transactionType,
            'value'               => $this->getPaymentMethod()->getProcessor()->getTransactionValue($this, $transactionType),
            'payment_transaction' => $this,
        );

        return $data;
    }

    /**
     * Get initial backend transaction (related to the first payment transaction)
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    public function getInitialBackendTransaction()
    {
        $bt = null;

        foreach ($this->getBackendTransactions() as $transaction) {
            if ($transaction->isInitial()) {
                $bt = $transaction;
                break;
            }
        }

        return $bt;
    }

    /**
     * Register transaction in order history
     *
     * @param string $suffix Suffix text to add to the end of event description
     *
     * @return void
     */
    public function registerTransactionInOrderHistory($suffix = null)
    {
        $descrSuffix = !empty($suffix) ? ' [' . static::t($suffix) . ']' : '';

        // Prepare event description
        $description = static::t($this->getHistoryEventDescription(), $this->getHistoryEventDescriptionData()) . $descrSuffix;

        if ($this->getStatus() == static::STATUS_FAILED && !$this->getDataCell('cart_items')) {
            // Failed transaction: Register info about cart items
            $this->setDataCell(
                'cart_items',
                serialize($this->getCartItems()),
                'Cart items'
            );

            \XLite\Core\Database::getEM()->flush($this);
        }

        // Run-time cache key
        $key = md5(
            $this->getOrder()->getOrderId() . '.'
            . $description . '.'
            . serialize($this->getEventData())
        );

        if (!isset($this->registeredCache[$key])) {

            // Register transaction in order history

            $this->registeredCache[$key] = true;

            \XLite\Core\OrderHistory::getInstance()->registerTransaction(
                $this->getOrder()->getOrderId(),
                $description,
                $this->getEventData()
            );

            if ($this->getStatus() == static::STATUS_FAILED) {
                // Send notification 'Failed transaction' to the Orders department
                \XLite\Core\Mailer::sendFailedTransactionAdmin($this);
            }
        }
    }

    /**
     * Get description of order history event (language label is returned)
     *
     * @return string
     */
    public function getHistoryEventDescription()
    {
        return 'Payment transaction X issued';
    }

    /**
     * Get data for description of order history event (substitution data for language label is returned)
     *
     * @return return
     */
    public function getHistoryEventDescriptionData()
    {
        return array(
            'trx_method' => static::t($this->getPaymentMethod()->getName()),
            'trx_type'   => static::t($this->getType()),
            'trx_value'  => $this->getOrder()->getCurrency()->roundValue($this->getValue()),
            'trx_status' => static::t($this->getReadableStatus()),
        );
    }

    /**
     * return event data
     *
     * @return array
     */
    public function getEventData()
    {
        $result = array();

        foreach ($this->getData() as $cell) {
            $result[] = array(
                'name'  => $cell->getLabel() ?: $cell->getName(),
                'value' => $cell->getValue()
            );
        }

        return $result;
    }

    /**
     * Check - transaction's method and specified method is equal or not
     *
     * @param \XLite\Model\Payment\Method $method Anothermethod
     *
     * @return boolean
     */
    public function isSameMethod(\XLite\Model\Payment\Method $method)
    {
        return $this->getPaymentMethod() && $this->getPaymentMethod()->getMethodId() == $method->getMethodId();
    }

    /**
     * Clone payment transaction
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function cloneEntity()
    {
        $newTransaction = parent::cloneEntity();

        $newTransaction->setCurrency($this->getCurrency());
        $newTransaction->setOrder($this->getOrder());
        $newTransaction->setPaymentMethod($this->getPaymentMethod());

        // Clone data cells
        foreach ($this->getData() as $data) {
            $cloned = $data->cloneEntity();
            $newTransaction->addData($cloned);
            $cloned->setTransaction($newTransaction);
        }

        // Clone backend transactions
        foreach ($this->getBackendTransactions() as $backend) {
            $cloned = $backend->cloneEntity();
            $newTransaction->addBackendTransactions($cloned);
            $cloned->setPaymentTransaction($newTransaction);
        }

        return $newTransaction;
    }

    /**
     * Returns transaction note
     *
     * @return string
     */
    public function getNote()
    {
        return !$this->note && $this->isFailed()
            ? static::getDefaultFailedReason()
            : $this->note;
    }

    /**
     * Get cart items array
     *
     * @return array
     */
    protected function getCartItems()
    {
        $result = array();

        foreach ($this->getOrder()->getItems() as $item) {
            $row = array();
            $row['name'] = $item->getName();
            $row['sku'] = $item->getSku();
            $row['price'] = $item->getPrice();
            $row['amount'] = $item->getAmount();

            if ($item->hasAttributeValues()) {
                foreach ($item->getSortedAttributeValues() as $attr) {
                    $row['attrs'][] = array(
                        'name'  => $attr->getActualName(),
                        'value' => $attr->getActualValue(),
                    );
                }

            } else {
                $row['attrs'] = array();
            }

            $result[] = $this->getCartItemData($item);
        }

        return $result;
    }

    /**
     * Get cart item data as an array
     *
     * @param \XLite\Model\OrderItem $item Order item object
     *
     * @return array
     */
    protected function getCartItemData($item)
    {
        $result = array();
        $result['name'] = $item->getName();
        $result['sku'] = $item->getSku();
        $result['price'] = $item->getPrice();
        $result['amount'] = $item->getAmount();

        if ($item->hasAttributeValues()) {
            foreach ($item->getSortedAttributeValues() as $attr) {
                $result['attrs'][] = array(
                    'name'  => $attr->getActualName(),
                    'value' => $attr->getActualValue(),
                );
            }

        } else {
            $result['attrs'] = array();
        }

        return $result;
    }

    /**
     * Get transaction_id
     *
     * @return integer 
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Transaction
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
     * Set publicTxnId
     *
     * @param string $publicTxnId
     * @return Transaction
     */
    public function setPublicTxnId($publicTxnId)
    {
        $this->publicTxnId = $publicTxnId;
        return $this;
    }

    /**
     * Get publicTxnId
     *
     * @return string 
     */
    public function getPublicTxnId()
    {
        return $this->publicTxnId;
    }

    /**
     * Set method_name
     *
     * @param string $methodName
     * @return Transaction
     */
    public function setMethodName($methodName)
    {
        $this->method_name = $methodName;
        return $this;
    }

    /**
     * Get method_name
     *
     * @return string 
     */
    public function getMethodName()
    {
        return $this->method_name;
    }

    /**
     * Set method_local_name
     *
     * @param string $methodLocalName
     * @return Transaction
     */
    public function setMethodLocalName($methodLocalName)
    {
        $this->method_local_name = $methodLocalName;
        return $this;
    }

    /**
     * Get method_local_name
     *
     * @return string 
     */
    public function getMethodLocalName()
    {
        return $this->method_local_name;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Transaction
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Transaction
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set public_id
     *
     * @param string $publicId
     * @return Transaction
     */
    public function setPublicId($publicId)
    {
        $this->public_id = $publicId;
        return $this;
    }

    /**
     * Get public_id
     *
     * @return string 
     */
    public function getPublicId()
    {
        return $this->public_id;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order
     * @return Transaction
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get payment_method
     *
     * @return \XLite\Model\Payment\Method 
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Add data
     *
     * @param \XLite\Model\Payment\TransactionData $data
     * @return Transaction
     */
    public function addData(\XLite\Model\Payment\TransactionData $data)
    {
        $this->data[] = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add backend_transactions
     *
     * @param \XLite\Model\Payment\BackendTransaction $backendTransactions
     * @return Transaction
     */
    public function addBackendTransactions(\XLite\Model\Payment\BackendTransaction $backendTransactions)
    {
        $this->backend_transactions[] = $backendTransactions;
        return $this;
    }

    /**
     * Get backend_transactions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBackendTransactions()
    {
        return $this->backend_transactions;
    }

    /**
     * Set currency
     *
     * @param \XLite\Model\Currency $currency
     * @return Transaction
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
