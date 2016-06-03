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

namespace XLite\Module\CDev\XPaymentsConnector\View\Settings;

/**
 * Welcome page (if X-Payments connector is not configured)
 */
class Welcome extends \XLite\Module\CDev\XPaymentsConnector\View\Settings\ASettings
{
    /** 
     * Scarry text displayed on the top of the page
     */
    const SCARY_TEXT = 'Give your customers – and yourself – peace of mind with this payment processing module 
                        that __MODULE__ guarantees compliance with PCI security mandates, significantly reduces 
                        the risk of data breaches and ensures you won’t be hit with a fine of up to $500,000 for 
                        non-compliance. Safely and conveniently store customers\' credit card information to use 
                        for new orders, reorders or recurring payments.';

    /**
     * Module plaeholder for scarry text
     */
    const SCARY_TEXT_PLACEHOLDER = '__MODULE__ ';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/welcome/script.js';

        return $list;
    }

    /**
     * Get passed payment method from the Add payment page 
     *
     * @return \XLite\Model\Payment\Method or null
     */
    protected static function getPaymentMethod()
    {
        $id = \XLite\Core\Request::getInstance()->method_id;

        $method = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($id)
            : null;

        if (
            $method
            && 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPaymentsAllowed' != $method->getClass()
        ) {

            $method = null;
        }

        return $method;
    }

    /**
     * Get passed payment method name 
     *
     * @return string
     */
    protected function getPaymentMethodName()
    {
        return $this->getPaymentMethod()
            ? $this->getPaymentMethod()->getName()
            : 'your gateway';
    }

    /**
     * Get scarry test
     *
     * @return string
     */
    protected function getScaryText()
    {
        $text = self::SCARY_TEXT;

        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod) {

            $text = str_replace(
                self::SCARY_TEXT_PLACEHOLDER, 
                'integrates <strong>' . $this->getPaymentMethodName() . '</strong> and <strong>X-Cart</strong>, ', 
                $text
            );

        } else {

            $text = str_replace(self::SCARY_TEXT_PLACEHOLDER, '', $text);

        }

        return $text;
    }

    /**
     * Logo URL: X-Payments or passed payment method 
     *
     * @return string
     */
    protected function getLogoUrl()
    {
        $url = $this->getDir() . '/img/xp_logo.png';

        $paymentMethod = $this->getPaymentMethod();

        if ($paymentMethod) {

            $url = '../../../' . $paymentMethod->getAdminIconURL();

        }

        return $url;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/welcome.tpl';
    }

    /**
     * Get link to X-Payments description
     *
     * @return array
     */
    public static function getConfigureLink()
    {
        return 'http://help.x-cart.com/index.php?title=X-Payments:User_manual#Payment_Configurations';
    }

    /**
     * Get link to X-Payments manual
     *
     * @return array
     */
    public static function getConnectLink()
    {
        return 'http://help.x-cart.com/index.php?title=X-Payments:User_manual#Online_Stores';
    }

    /**
     * Get link to purchase X-Payments
     *
     * @return string 
     */
    public static function getFreeTrialLink()
    {
        $utm = '&utm_source=xp_connector_xc5'
            . '&utm_medium=xp_connector_xc5_welcome_page'
            . '&utm_campaign=xp_connector_xc5';


        $email = urlencode(\XLite\Core\Auth::getInstance()->getProfile()->getLogin());
        $url = parse_url(\XLite::getInstance()->getShopURL());

        $domain = urlencode($url['host']);

        return 'http://www.x-cart.com/x-payments-trial-demo.html?'
                . 'your_email=' . $email . '&shop_url=' . $domain . $utm;
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return boolean
     */
    public function getPages()
    {
        return array(\XLite\Module\CDev\XPaymentsConnector\Core\Settings::PAGE_WELCOME);
    }

    /**
     * Display connection steps or not 
     *
     * @return boolean
     */
    public function isDisplaySteps()
    {
        return !\XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()->isModuleConfigured();
    }

    /**
     * Link to add new payment configuration at X-Payments
     *
     * @return string 
     */
    public function getAddPaymentConfLink()
    {
        $id = \XLite\Core\Request::getInstance()->method_id;

        $method = $id
            ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($id)
            : null;

        if (
            $method
            && 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPaymentsAllowed' == $method->getClass()
        ) {

            $xpModuleClass = $method->getServiceName();
            $xpModuleClass = str_replace('XPayments.Allowed.XPay_Module_', '', $xpModuleClass);
        }

        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_xpayments_url . 'admin.php?target=payment_conf&module_class=' . $xpModuleClass;
    }

}
