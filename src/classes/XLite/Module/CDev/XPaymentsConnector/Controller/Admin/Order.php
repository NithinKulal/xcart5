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
