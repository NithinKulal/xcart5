<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Return to the store in X-Payments's iframe 
 *
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{
    /**
     * Detect the X-Payments transaction from the request directly
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectXpcTransaction()
    {
        $txnId = \XLite\Core\Request::getInstance()->txnId;

        $transaction = null;

        if ($txnId) {
            $transactionData = \XLite\Core\Database::getRepo('XLite\Model\Payment\TransactionData')
                ->findOneBy(array('value' => $txnId, 'name' => 'xpc_txnid'));
            if ($transactionData) {
                $transaction = $transactionData->getTransaction();
            }
        }

        return $transaction;
    }

    /**
     * Return
     *
     * @return void
     */
    protected function doActionReturn()
    {
        $transaction = $this->detectXpcTransaction();

        if ($transaction) {
            
            if (
                \XLite\Core\Request::getInstance()->last_4_cc_num
                && \XLite\Core\Request::getInstance()->card_type
                && !$transaction->getCard()
            ) {
                $transaction->saveCard(
                    '******',
                     \XLite\Core\Request::getInstance()->last_4_cc_num,
                    \XLite\Core\Request::getInstance()->card_type
                );
            }

            $profile = $transaction->getOrder()->getOrigProfile();

            if ($profile) {

                $address = null;

                if ($profile->getBillingAddress()) {
                    $address = $profile->getBillingAddress();
                } elseif ($profile->getShippingAddress()) {
                    $address = $profile->getShippingAddress();
                }

                if (
                    $address
                    && $transaction->getXpcData()
                ) {
                    $transaction->getXpcData()->setBillingAddress($address);
                }

                // If default card is not set then set it to new one
                if (
                    !$profile->getDefaultCardId()
                    && $transaction->getXpcData()
                ) {
                    $profile->setDefaultCardId($transaction->getXpcData()->getId());
                }

            }

            \XLite\Core\Database::getEM()->flush();

            $this->getIframe()->enable();
        }

        parent::doActionReturn();
    }

}
