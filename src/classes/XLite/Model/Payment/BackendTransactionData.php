<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

/**
 * Backend transaction data storage
 *
 * @Entity
 * @Table (name="payment_backend_transaction_data",
 *      indexes={
 *          @Index (name="tn", columns={"backend_transaction_id","name"})
 *      }
 * )
 */
class BackendTransactionData extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer")
     */
    protected $data_id;

    /**
     * Record name
     *
     * @var string
     *
     * @Column (type="string", length=128)
     */
    protected $name;

    /**
     * Record public name
     *
     * @var string
     *
     * @Column (type="string", length=255)
     */
    protected $label = '';

    /**
     * Access level
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $access_level = \XLite\Model\Payment\TransactionData::ACCESS_ADMIN;

    /**
     * Value
     *
     * @var string
     *
     * @Column (type="text")
     */
    protected $value;

    /**
     * Transaction
     *
     * @var \XLite\Model\Payment\BackendTransaction
     *
     * @ManyToOne  (targetEntity="XLite\Model\Payment\BackendTransaction", inversedBy="data")
     * @JoinColumn (name="backend_transaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $transaction;

    /**
     * Check record availability
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return (\XLite::isAdminZone() && \XLite\Model\Payment\TransactionData::ACCESS_ADMIN == $this->getAccessLevel())
            || \XLite\Model\Payment\TransactionData::ACCESS_CUSTOMER == $this->getAccessLevel();
    }

    /**
     * Get data_id
     *
     * @return integer 
     */
    public function getDataId()
    {
        return $this->data_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return BackendTransactionData
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return BackendTransactionData
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set access_level
     *
     * @param string $accessLevel
     * @return BackendTransactionData
     */
    public function setAccessLevel($accessLevel)
    {
        $this->access_level = $accessLevel;
        return $this;
    }

    /**
     * Get access_level
     *
     * @return string 
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * Set value
     *
     * @param text $value
     * @return BackendTransactionData
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return text 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set transaction
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     * @return BackendTransactionData
     */
    public function setTransaction(\XLite\Model\Payment\BackendTransaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\BackendTransaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
