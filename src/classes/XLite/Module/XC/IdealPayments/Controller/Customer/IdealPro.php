<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\IdealPayments\Controller\Customer;

/**
 * Ideal Professional page controller
 * This page is only used to redirect customer to iDEAL side
 */
class IdealPro extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Do redirect customer to iDEAL server for payment
     *
     * @return void
     */
    protected function doActionTransaction()
    {
        try {

            $processor = new \XLite\Module\XC\IdealPayments\Model\Payment\Processor\IdealProfessional();

            $processor->doTransactionRequest(
                \XLite\Core\Request::getInstance()->iid,
                \XLite\Core\Request::getInstance()->transid
            );

        } catch (\Exception $e) {

            \XLite\Core\TopMessage::addError(
                static::t('Something wrong in the iDEAL payment module settings. Please try later or use other payment option.')
            );

            $this->setReturnURL($this->buildURL('checkout'));
        }
    }
}
