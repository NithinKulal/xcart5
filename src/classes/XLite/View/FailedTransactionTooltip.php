<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Payment transactions items list
 */
class FailedTransactionTooltip extends \XLite\View\AView
{
    /**
     * Preprocess printed value and output it
     *
     * @param \XLite\Model\Payment\TransactionData $cell Transaction data cell
     *
     * @return string
     */
    public function getCellValue($cell)
    {
        $widgets = $this->defineCellWidgets($cell);

        if (isset($widgets[$cell->getName()])) {
            $widget = $widgets[$cell->getName()];
            $value = $widget->getContent();

        } else {
            $value = $cell->getValue();
        }

        return $value;
    }

    /**
     * Defines key-value based storage for widgets, where key = cell name and value = view object, child of XLite\View\Payment\TransactionDataValue
     *
     * @param \XLite\Model\Payment\TransactionData $cell Transaction data cell
     *
     * @return string
     */
    protected function defineCellWidgets($cell)
    {
        return array(
            'cart_items' => $this->getWidget(
                array(
                    'cell' => $cell,
                ),
                'XLite\View\Payment\TransactionDataValue\CartItems'
            ),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment_transactions/parts/cell.transaction_status.tooltip.twig';
    }
}
