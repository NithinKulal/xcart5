<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment;

/**
 * X-Payments payment transaction data
 *
 * @Entity
 * @Table  (name="xpc_payment_transaction_data")
 */

class XpcTransactionData extends \XLite\Model\AEntity
{
    /**
     * Allow card usage for recharges 
     */
    const RECHARGE_TRUE  = 'Y';
    const RECHARGE_FALSE = 'N';

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Masked credit card number 
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_number = '';

    /**
     * Type of the credit card
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_type = '';

    /**
     * Credit card epiration date
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $card_expire = '';

    /**
     * Allow card usage for recharges 
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $use_for_recharges = self::RECHARGE_FALSE;

    /**
     * Billing address 
     *
     * @var \XLite\Model\Address 
     *
     * @ManyToOne  (targetEntity="XLite\Model\Address")
     * @JoinColumn (name="address_id", referencedColumnName="address_id", onDelete="SET NULL")
     */
    protected $billingAddress;

    /**
     * One-to-one relation with payment transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @OneToOne  (targetEntity="XLite\Model\Payment\Transaction", inversedBy="xpc_data")
     * @JoinColumn (name="transaction_id", referencedColumnName="transaction_id", onDelete="CASCADE")
     */
    protected $transaction;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set card_number
     *
     * @param string $cardNumber
     * @return XpcTransactionData
     */
    public function setCardNumber($cardNumber)
    {
        $this->card_number = $cardNumber;
        return $this;
    }

    /**
     * Get card_number
     *
     * @return string 
     */
    public function getCardNumber()
    {
        return $this->card_number;
    }

    /**
     * Set card_type
     *
     * @param string $cardType
     * @return XpcTransactionData
     */
    public function setCardType($cardType)
    {
        $this->card_type = $cardType;
        return $this;
    }

    /**
     * Get card_type
     *
     * @return string 
     */
    public function getCardType()
    {
        return $this->card_type;
    }

    /**
     * Set card_expire
     *
     * @param string $cardExpire
     * @return XpcTransactionData
     */
    public function setCardExpire($cardExpire)
    {
        $this->card_expire = $cardExpire;
        return $this;
    }

    /**
     * Get card_expire
     *
     * @return string 
     */
    public function getCardExpire()
    {
        return $this->card_expire;
    }

    /**
     * Set use_for_recharges
     *
     * @param string $useForRecharges
     * @return XpcTransactionData
     */
    public function setUseForRecharges($useForRecharges)
    {
        $this->use_for_recharges = $useForRecharges;
        return $this;
    }

    /**
     * Get use_for_recharges
     *
     * @return string 
     */
    public function getUseForRecharges()
    {
        return $this->use_for_recharges;
    }

    /**
     * Set billingAddress
     *
     * @param \XLite\Model\Address $billingAddress
     * @return XpcTransactionData
     */
    public function setBillingAddress(\XLite\Model\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \XLite\Model\Address 
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     * @return XpcTransactionData
     */
    public function setTransaction(\XLite\Model\Payment\Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
