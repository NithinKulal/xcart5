<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment;

/**
 * XPayments payment processor
 *
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * Fields for transaction note is limited by 255 characters in database
     */
    const NOTE_LIMIT = 255;

    /**
     * One-to-one relation with X-Payments transaction details
     *
     * @var \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData
     *
     * @OneToOne  (targetEntity="\XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData", mappedBy="transaction", cascade={"all"})
     */
    protected $xpc_data;

    /**
     * One-to-one relation with X-Payments payment fraud check data
     *
     * @var \XLite\Module\CDev\XPaymentsConnector\Model\Payment\FraudCheckData
     *
     * @OneToMany  (targetEntity="\XLite\Module\CDev\XPaymentsConnector\Model\Payment\FraudCheckData", mappedBy="transaction", cascade={"all"})
     */
    protected $fraud_check_data;

    /**
     * Check - transaction is X-Payment connector's transaction
     * 
     * @return boolean
     */
    public function isXpc($includeSavedCardsMethod = false)
    {
        $xpClass = array(
            'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPayments',
        );

        if ($includeSavedCardsMethod) {
            $xpClass[] = 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\SavedCard';
        } 

        return $this->getPaymentMethod()
            && in_array($this->getPaymentMethod()->getClass(), $xpClass);
    }

    /**
     * Check - transaction is open or not
     *
     * @return boolean
     */
    public function isOpen()
    {
        return parent::isOpen()
            || (static::STATUS_INPROGRESS == $this->getStatus() && $this->isXpc(false));
    }

    /**
     * Save card in details
     *
     * @return array
     */
    public function saveCard($first6, $last4, $type, $expireMonth = '', $expireYear = '')
    {
        if (!$this->getXpcData()) {
            
            $xpcData = new \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData;
            $xpcData->setTransaction($this);

            $this->xpc_data = $xpcData;
        }

        $xpcData = $this->getXpcData();

        $xpcData->setCardNumber($first6 . '******' . $last4);
        $xpcData->setCardType($type);
        
        if ($expireMonth && $expireYear) {

            // If this is changed, correct Model\Payment\Processor\AXPayments::copyMaskedCard() as well
            $xpcData->setCardExpire($expireMonth . '/' . $expireYear);
        }
    }

    /**
     * Get saved credit card 
     *
     * @return array
     */
    public function getCard($forRechargesOnly = false)
    {
        $result = false;

        if (
            $this->getXpcData()
            && (
                !$forRechargesOnly 
                || 'Y' == $this->getXpcData()->getUseForRecharges()
            )
        ) {

            $result = array(
                'card_id'           => $this->getXpcData()->getId(),
                'card_number'       => $this->getXpcData()->getCardNumber(),
                'card_type'         => $this->getXpcData()->getCardType(),
                'card_type_css'     => strtolower($this->getXpcData()->getCardType()),
                'use_for_recharges' => $this->getXpcData()->getUseForRecharges(),
                'expire'            => $this->getXpcData()->getCardExpire(),
            );
        }

        return $result;
    }

    /**
     * Get initial action of a transaction. Was it first authorized or charged.
     *
     * @return string
     */
    public function getInitXpcAction()
    {
        if (
            $this->getDataCell('xpc_authorized')
            && $this->getDataCell('xpc_authorized')->getValue() > \XLite\Model\Order::ORDER_ZERO
        ) {
            $action = 'authorize';
        } else {
            $action = 'charge';
        }

        return $action;
    }

    /**
     * Get transaction xpc_ values. What was actually authorized, paid, voided, and refunded.
     *
     * @return array 
     */
    public function getXpcValues()
    {
        $fields = array(
            'authorized', 'paid', 'voided', 'refunded',
        );

        foreach ($fields as $key) {
            $$key = $this->getDataCell('xpc_' . $key)
                ? $this->getDataCell('xpc_' . $key)->getValue()
                : 0;

        }

        if ($paid == 0 && $refunded > 0) {

            // WA fix for the information returned from X-Payments.
            // The captured and charged values are calculated in a different way,
            // refunded amount is substracted from charged but not from captured.
            // See XPay_Model_Payment::getInfo().

            $paid = $refunded;
        }

        return array($authorized, $paid, $voided, $refunded);
    }

    /**
     * Get charge value modifier
     *
     * @return float
     */
    public function getChargeValueModifier()
    {
        if ($this->isXpc(true)) {

            list($authorized, $paid, $voided, $refunded) = $this->getXpcValues();

            $positive = max($authorized, $paid);

            $negative = $voided + $refunded;

            $value = $positive - $negative;

        } else {

            $value = parent::getChargeValueModifier();

        }

        return $value;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Transaction
     */
    public function setNote($note)
    {
        if (strlen($note) > self::NOTE_LIMIT) {

            // truncate note for STRICT_TRANS_TABLES
            $note = function_exists('mb_substr')
                ? mb_substr($value, 0, self::NOTE_LIMIT - 3)
                : substr($value, 0, self::NOTE_LIMIT - 3);

            $note .= '...';
        }

        return parent::setNote($note);
    }

    /**
     * Set xpc_data
     *
     * @param \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData $xpcData
     * @return Transaction
     */
    public function setXpcData(\XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData $xpcData = null)
    {
        $this->xpc_data = $xpcData;
        return $this;
    }

    /**
     * Get xpc_data
     *
     * @return \XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData 
     */
    public function getXpcData()
    {
        return $this->xpc_data;
    }

    /**
     * Add fraud_check_data
     *
     * @param \XLite\Module\CDev\XPaymentsConnector\Model\Payment\FraudCheckData $fraudCheckData
     * @return Transaction
     */
    public function addFraudCheckData(\XLite\Module\CDev\XPaymentsConnector\Model\Payment\FraudCheckData $fraudCheckData)
    {
        $this->fraud_check_data[] = $fraudCheckData;
        return $this;
    }

    /**
     * Get fraud_check_data
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFraudCheckData()
    {
        return $this->fraud_check_data;
    }
}
