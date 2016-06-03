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

namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Return to the store in X-Payments's iframe 
 *
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn implements \XLite\Base\IDecorator
{
    /**
     * Return
     *
     * @return void
     */
    protected function doActionReturn()
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

            $address = null;

            if ($profile->getBillingAddress()) {
                $address = $profile->getBillingAddress();
            } elseif ($profile->getShippingAddress()) {
                $address = $profile->getShippingAddress();
            }

            if ($address) {
                $transaction->getXpcData()->setBillingAddress($address);
            }

            \XLite\Core\Database::getEM()->flush();

            $this->getIframe()->enable();
        }

        parent::doActionReturn();
    }

}
