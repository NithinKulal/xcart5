<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Admin;

/**
 * Payment settings 
 */
class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings implements \XLite\Base\IDecorator
{
    /**
     * Add method
     *
     * @return void
     */
    protected function doActionAdd()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        $method = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($id)
            : null;

        if (
            $method
            && 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPaymentsAllowed' == $method->getClass()
        ) {

            $this->setReturnURL($this->buildURL(
                'xpc', 
                '', 
                array(
                    'page' => \XLite\Module\CDev\XPaymentsConnector\Core\Settings::PAGE_WELCOME,
                    'method_id' => $id
                )
            ));

        } else {

            parent::doActionAdd();

            $classes = array(
                'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPayments',
                'Module\CDev\XPaymentsConnector\Model\Payment\Processor\SavedCard',
            );

            if (
                $method 
                && in_array($method->getClass(), $classes)) 
            {
                $this->setReturnURL($this->buildURL('payment_settings'));
            }
        }
    }

    /**
     * Handle request 
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            \XLite\Core\Request::getInstance()->action != 'add'
            && \XLite\Module\CDev\XPaymentsConnector\Core\Settings::getInstance()->checkUpdateAllowedModules()
        ) {
            \XLite\Module\CDev\XPaymentsConnector\Core\Settings::getInstance()->importAllowedModules();
        }

        parent::handleRequest();
    }
}
