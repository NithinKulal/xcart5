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

namespace XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model\Order;

/**
 * List of XPC transactions and cards 
 */
class Transactions extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Column name
     */
    const COLUMN_NAME = 'transaction';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            self::COLUMN_NAME => array(
                static::COLUMN_TEMPLATE => 'modules/CDev/XPaymentsConnector/order/transactions/transaction.tpl',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_MAIN     => true,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * isEmptyListTemplateVisible
     *
     * @return string
     */
    protected function isEmptyListTemplateVisible()
    {
        return false;
    }

    // }}}

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    // }}}

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isTableHeaderVisible()
    {
        return false;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\BackendTransaction';

        $cnd->{$class::SEARCH_ORDER_ID} = $this->getOrder()->getOrderId();

        return $cnd;
    }

    // }}}

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        $class = parent::getColumnClass($column, $entity);

        if (self::COLUMN_NAME == $column[static::COLUMN_CODE]) {
            $class .= ' card ' . strtolower($entity->getCardType());
        }

        return $class;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getPaymentURL(\XLite\Model\AEntity $entity)
    {
        $result = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_xpayments_url
            . 'admin.php';

        if (
            $entity->getTransaction()
            && $entity->getTransaction()->getDataCell('xpc_txnid')
        ) {
            $result .= '?target=payment&txnid=' . $entity->getTransaction()->getDataCell('xpc_txnid')->getValue();
        }

        return $result;
    }

    /**
     * Get transaction Id 
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getTransactionId(\XLite\Model\AEntity $entity)
    {
        return $entity->getTransaction()->getTransactionId();
    }

    /**
     * Get transaction
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return \XLite\Model\Payment\Transaction or null 
     */
    protected function getTransaction(\XLite\Model\AEntity $entity)
    {
        return $entity->getTransaction();
    }

    /**
     * Payment transaction units
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    protected function getTransactionUnits(\XLite\Model\AEntity $entity)
    {
        $transaction = $entity->getTransaction();

        $result = false;

        if ($transaction) {
            $view = new \XLite\View\Order\Details\Admin\PaymentActions;

            $result = $view->getUnitsForTransaction($transaction);
        }

        return $result;
    }

    /**
     * Get card number. Adds saved flag for saved ones
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return \XLite\Model\Payment\Transaction or null
     */
    protected function getCardNumber(\XLite\Model\AEntity $entity)
    {
        $result = $entity->getCardNumber();

        if (
            $entity->getTransaction()
            && $entity->getTransaction()->getPaymentMethod()
            && $entity->getTransaction()->getPaymentMethod()->getClass() == 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\SavedCard'
        ) {
            $result .= ' (Saved)';
        }

        return $result;
    }

    /**
     * Is transaction marked as fraud
     *
     * @return bool
     */
    public function isFraudStatus(\XLite\Model\AEntity $entity)
    {
        $transaction = $entity->getTransaction();

        return $transaction->getDataCell('xpc_is_fraud_status')
            && $transaction->getDataCell('xpc_is_fraud_status')->getValue();

    }
}
