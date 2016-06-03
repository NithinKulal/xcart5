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
