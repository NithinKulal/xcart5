<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View;

/**
 * Order history widget
 */
class OrderHistory extends \XLite\View\OrderHistory implements \XLite\Base\IDecorator
{
    /**
     * Details getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return array
     */
    protected function getDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        $list = parent::getDetails($event);

        if ($list && $this->hasXpcTransactions()) {
            // For now just sort the list alphabetically.
            sort($list[0]);
        }

        return $list;
    }

    /**
     * Get number of columns to display event details
     *
     * @return integer
     */
    protected function getColumnsNumber()
    {
        $result = parent::getColumnsNumber();

        if ($this->hasXpcTransactions()) {

            // 1) There is no room for three columns
            // 2) Even two columns look ugly
            $result = 1;
        }

        return $result;
    }

    /**
     * Check if order has X-Payments transactions
     *
     * @return bool
     */
    protected function hasXpcTransactions()
    {
        $result = false;

        $transactions = $this->getOrder()->getPaymentTransactions();

        foreach ($transactions as $t) {

            if ($t->isXpc(true)) {

                $result = true;
                break;
            }
        }

        return $result;
    }
}
