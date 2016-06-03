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
