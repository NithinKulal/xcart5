<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Admin;

/**
 * Order
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Get X-Payments connector transactions 
     * 
     * @return boolean
     */
    public function getXpcTransactions()
    {
        $cnd = new \XLite\Core\CommonCell;
        $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\BackendTransaction';

        $cnd->{$class::SEARCH_ORDER_ID} = $this->getOrder()->getOrderId();

        $count = \XLite\Core\Database::getRepo('XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData')
            ->search($cnd, true);

        return $count > 0;
    }

    /**
     * Do Recharge action
     *
     * @return void 
     */
    public function doActionRecharge()
    {
        if (
            \XLite\Core\Request::getInstance()->trn_id
            && \XLite\Core\Request::getInstance()->amount
            && $this->getOrder()
        ) {
    
            $parentCardTransaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find(\XLite\Core\Request::getInstance()->trn_id);
            $amount = number_format(\XLite\Core\Request::getInstance()->amount, 2, '.', '');

            $parentCardTransaction->getPaymentMethod()->getProcessor()->doRecharge(
                $this->getOrder(),
                $parentCardTransaction,
                $amount
            );
        }

        $this->redirectBackToOrder();
    }

    /**
     * Order number wrapper 
     *
     * @return integer
     */
    public function getOrderNumber() 
    {
        return $this->getOrder()->getOrderNumber();
    }

    /**
    * Get difference text label
    *
    * @return string
    */
    public function getDifferenceLabel()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Model\Order::getDifferenceLabel();
    }

    /**
     * Redirect admin back to the order page (controller's redirecter wrapper) 
     *
     * @return void
     */
    public function redirectBackToOrder() 
    {
        $this->setHardRedirect();

        $this->setReturnURL(
            $this->buildURL(
                'order',
                '',
                array(
                    'order_number'  => $this->getOrderNumber(),
                )
            )
        );

        $this->doRedirect();

        exit;
    }
}
