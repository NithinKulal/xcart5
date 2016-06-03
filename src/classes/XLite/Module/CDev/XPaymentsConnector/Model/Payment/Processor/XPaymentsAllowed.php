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

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor;

/**
 * Fake payment method. Used for X-Payments allowed payment methods list if connector is not configured
 */
class XPaymentsAllowed extends \XLite\Module\CDev\XPaymentsConnector\Model\Payment\Processor\AXPayments
{

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return false;
    }

    /**
     * Get payment method configuration page URL
     *
     * @param \XLite\Model\Payment\Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel. Additional init configuration can be provided
     *
     * @return string
     */
    public function getConfigurationURL(\XLite\Model\Payment\Method $method, $justAdded = false)
    {
        return ($this->getModule() && $this->getModule()->getModuleId())
            ? \XLite\Core\Converter::buildURL(
                'xpc',
                '',
                array('section' => 'welcome')
            )
            : parent::getConfigurationURL($method, $justAdded);
    }


    /**
     * This is not Saved Card payment method
     *
     * @return boolean
     */
    protected function isSavedCardsPaymentMethod()
    {
        return false;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return '';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        return array();
    }
}
