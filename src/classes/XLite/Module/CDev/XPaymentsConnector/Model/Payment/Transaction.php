<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
}
