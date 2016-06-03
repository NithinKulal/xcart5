<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Payment method data (saved with order) modification (usage: AOM)
 */
class PaymentMethodData extends \XLite\View\AView
{
    /**
     * Cached processor
     *
     * @var \XLite\Model\Payment\Base\Processor
     */
    protected $processor = null;

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment_method_data/body.twig';
    }

    /**
     * Get container attributes 
     * 
     * @return array
     */
    protected function getContainerAttributes()
    {
        $attributes = array(
            'class' => array('order-payment-data-dialog'),
        );

        return $attributes;
    }

    /**
     * Get transaction ID
     *
     * @return integer
     */
    protected function getTransactionId()
    {
        return (int) \XLite\Core\Request::getInstance()->transaction_id;
    }

    /**
     * Get list of field values
     *
     * @return array
     */
    protected function getFieldValues()
    {
        $result = array();

        $transactionId = $this->getTransactionId();

        $transaction = $transactionId
            ? \XLite\Core\Database::getRepo('\XLite\Model\Payment\Transaction')->find($transactionId)
            : null;

        if (
            $transaction
            && $transaction->getPaymentMethod()
            && $transaction->getPaymentMethod()->getProcessor()
        ) {
            $transactionData = $transaction->getPaymentMethod()->getProcessor()->getTransactionData($transaction);

            if ($transactionData) {

                $prefix = 'transaction-' . $transaction->getTransactionId();

                $requestData = \XLite\Core\Request::getInstance()->$prefix;

                foreach($transactionData as $data) {

                    $result[] = array(
                        'name' => 'orig-' . $data['name'],
                        'value' => isset($requestData[$data['name']])
                                ? $requestData[$data['name']]
                                : $data['value'],
                    );
                }
            }
        }

        return $result;
    }
}
