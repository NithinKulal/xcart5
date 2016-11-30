<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

/**
 * Payment actions unit widget (button capture or refund or void etc)
 *
 * @ListChild (list="order.details.payment_actions", zone="admin")
 */
class PaymentActionsUnit extends \XLite\View\AView
{
    /**
     *  Widget parameter names
     */
    const PARAM_TRANSACTION = 'transaction';
    const PARAM_UNIT        = 'unit';
    const PARAM_DISPLAY_SEPARATOR = 'displaySeparator';

    /**
     * Cache of unit message value
     *
     * @var string
     */
    protected $message = null;

    /**
     * Payment action units that need confirmation
     * 
     * @return array
     */
    protected function needConfirm()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND
        );
    }

    /**
     * Payment action units that need amount
     *
     * @return array
     */
    protected function needAmount()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        );
    }

    /**
     * Payment action units that need amount
     *
     * @return array
     */
    protected function isNeedAmount()
    {
        return in_array($this->getParam(self::PARAM_UNIT), $this->needAmount());
    }

    /**
     * Return refund maximum value
     *
     * @return array
     */
    protected function getRefundValue()
    {
        return $this->getParam(self::PARAM_TRANSACTION)->getChargeValueModifier();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/order/payment_actions/unit.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_TRANSACTION => new \XLite\Model\WidgetParam\TypeObject('Transaction', null, false, 'XLite\Model\Payment\Transaction'),
            self::PARAM_UNIT        => new \XLite\Model\WidgetParam\TypeString('Unit', '', false),
            self::PARAM_DISPLAY_SEPARATOR => new \XLite\Model\WidgetParam\TypeBool('Display separator', false, false),
        );
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return 'action payment-action-button';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getParam(self::PARAM_TRANSACTION)
            && $this->isTransactionUnitAllowed(
                $this->getParam(self::PARAM_TRANSACTION),
                $this->getParam(self::PARAM_UNIT)
            );
    }

    /**
     * Return true if requested unit is allowed for the transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     * @param string                           $unit        Unit
     *
     * @return boolean
     */
    protected function isTransactionUnitAllowed($transaction, $unit)
    {
        return $transaction->getPaymentMethod()->getProcessor()->isTransactionAllowed($transaction, $unit);
    }

    /**
     * Return true if separator should be displayed after the button
     *
     * @return boolean
     */
    protected function isDisplaySeparator()
    {
        return $this->getParam(self::PARAM_DISPLAY_SEPARATOR);
    }

    /**
     * Get unit name (for button naming)
     *
     * @return string
     */
    protected function getUnitName()
    {
        return ucfirst($this->getParam(self::PARAM_UNIT));
    }

    /**
     * Button widget class
     * 
     * @return string
     */
    protected function getButtonWidgetClass(){
        $class = '\XLite\View\Button\Regular';

        if (in_array($this->getParam(self::PARAM_UNIT), $this->needConfirm())) {
            $class = '\XLite\View\Button\ConfirmRegular';
        }

        if (in_array($this->getParam(self::PARAM_UNIT), $this->needAmount())) {
            $class = '\XLite\View\FormField\Input\RefundMultiple';
        }

        return $class;
    }

    /**
     * Get action URL
     *
     * @return string
     */
    protected function getActionURL()
    {
        return $this->buildURL(
            'order',
            $this->getParam(self::PARAM_UNIT),
            array(
                'order_number' => $this->getParam(self::PARAM_TRANSACTION)->getOrder()->getOrderNumber(),
                'trn_id'       => $this->getParam(self::PARAM_TRANSACTION)->getTransactionId(),
            )
        );
    }

    /**
     * Return true if warning message should be displayed
     *
     * @return boolean
     */
    protected function hasWarning()
    {
        return (bool) $this->getWarningMessage();
    }

    /**
     * Get warning message
     *
     * @return string
     */
    protected function getWarningMessage()
    {
        $transaction = $this->getParam(self::PARAM_TRANSACTION);

        if ($transaction && !isset($this->message)) {
            $this->message = $transaction->getPaymentMethod()->getProcessor()->getTransactionMessage(
                $transaction,
                $this->getParam(self::PARAM_UNIT)
            );
        }

        return $this->message;
    }
}
