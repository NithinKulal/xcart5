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
 * X-Payments Connector module settings
 *
 */
class Xpc extends \XLite\Controller\Admin\Module
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Instance of settings 
     *
     * @var \XLite\Module\CDev\XPaymentsConnector\Core\Settings
     */
    protected $settings = null;

    /**
     * Initialize settings 
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function init()
    {
        parent::init();

        $this->settings = \XLite\Module\CDev\XPaymentsConnector\Core\Settings::getInstance();
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        parent::handleRequest();

        if (!$this->settings->isPageValid(\XLite\Core\Request::getInstance()->page)) {

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'xpc',
                    '',
                    array(
                        'page'  => $this->settings->getDefaultPage(),
                    )
                )
            );

            $this->doRedirect();
        }
    }

    /**
     * Get current module ID
     *
     * @return integer
     */   
    public function getModuleID()
    {
        if (!isset($this->moduleID)) {
            $module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy(
                array(
                    'name' => 'XPaymentsConnector',
                    'author' => 'CDev',
                    'installed' => 1,
                    'enabled' => 1,
                )
            );

            if ($module) {
                $this->moduleID = $module->getModuleID();
                $this->module = $module;
            }
        }

        return $this->moduleID;
    }

    /**
     * Check if connection to X-Payments is OK 
     *
     * @return boolean
     */
    public function isConnected()
    {
        $settings = $this->settings;

        return $settings::RESULT_FAILED !== $settings->testConnection();
    }

    /**
     * Wrapper for X-Payments client isModuleConfigured() method 
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance()->isModuleConfigured();
    }

    /**
     * Check - is there are any actve payment methods which can save cards 
     *
     * @return boolean
     */
    public function hasActiveMethodsSavingCards()
    {
        $paymentMethods = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findAllActive();

        $result = false;

        foreach ($paymentMethods as $pm) {
            if (
                'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPayments' == $pm->getClass()
                && 'Y' == $pm->getSetting('saveCards')
            ) {
                $result = true;
                break;
            }
        }


        return $result;
    }

    /**
     * Check - is payment configurations imported early or not
     *
     * @return boolean
     */
    public function hasPaymentMethods()
    {
        return $this->settings->hasPaymentMethods();
    }

    /**
     * Get payment methods
     *
     * @return array
     */
    public function getPaymentMethods($processor = 'XPayments')
    {
        return $this->settings->getPaymentMethods($processor);
    }

    /**
     * Update payment methods: save cards, currency, etc 
     *
     * @return void
     */
    protected function doActionUpdatePaymentMethods()
    {
        $methods = $this->getPaymentMethods();

        $request = \XLite\Core\Request::getInstance()->data;

        $config = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector;

        $saveCardsMethodSubmitted = false;

        foreach ($methods as $method) {

            $pmData = \XLite\Core\Request::getInstance()->data[$method->getMethodId()];

            if (
                isset($pmData['save_cards'])
                && 'Y' == $pmData['save_cards']
                && 'Y' == $method->getSetting('canSaveCards')
            ) {
                $method->setSetting('saveCards', 'Y');

                if (
                    !empty($pmData['enabled'])
                    && !$saveCardsMethodSubmitted
                ) {
                    // The second condition is not necesary.
                    // But in this case noone will ask,
                    // why the last method is set for zero auth instead of first
                    $saveCardsMethodSubmitted = $method;
                }

            } else {
                $method->setSetting('saveCards', 'N');
            }

            if (
                isset($pmData['currency'])
                && $pmData['currency']
            ) {
                $method->setSetting('currency', $pmData['currency']);
            }

            if (
                isset($pmData['enabled'])
                && $pmData['enabled']
            ) {
                $method->setEnabled(true);

            } else {
                $method->setEnabled(false);
            }
        
        }

        $saveCardsMethodInStore = $this->getPaymentMethods('SavedCard');

        if (
            !$saveCardsMethodInStore
            && $saveCardsMethodSubmitted
        ) {

            // Add Saved credit card payment method if at least one of X-Payments payment methods saves cards   
            $pm = new \XLite\Model\Payment\Method;
            \XLite\Core\Database::getEM()->persist($pm);
            $pm->setClass('Module\CDev\XPaymentsConnector\Model\Payment\Processor\SavedCard');
            $pm->setServiceName('SavedCard');
            $pm->setName('Use a saved credit card');
            $pm->setType(\XLite\Model\Payment\Method::TYPE_CC_GATEWAY);
            $pm->setAdded(true);
            $pm->setEnabled(true);

        } elseif (
            $saveCardsMethodInStore
            && !$saveCardsMethodSubmitted
        ) {
            // Remove Seved credit card payment method if all X-Payments payment methods do not save cards
            foreach ($saveCardsMethodInStore as $pm) {
                \XLite\Core\Database::getEM()->remove($pm);
            }
        }

        // Configure the Zero Auth if it's not done yet
        if (
            $saveCardsMethodSubmitted
            && !\XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->allowZeroAuth()
            && !\XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::DISABLED == $config->xpc_zero_auth_method_id
        ) {
            $settings = array(
                'xpc_zero_auth_method_id' => $saveCardsMethodSubmitted->getMethodId(),
            );

            if (!$config->xpc_zero_auth_amount) {
                $settings['xpc_zero_auth_amount'] = '1.00';
            }
            if (!$config->xpc_zero_auth_description) {
                $settings['xpc_zero_auth_description'] = \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getDefaultDescription();
            }

            foreach ($settings as $key => $value) {

                $setting = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                    array(
                       'name' => $key,
                       'category' => 'CDev\XPaymentsConnector'
                    )
                );

                \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                    $setting,
                    array('value' => $value)
                );
            }
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Config::updateInstance();
    }

    /**
     * Deploy configuration
     *
     * @return void
     */
    protected function doActionDeployConfiguration()
    {
        $errorMsg = $this->settings->deployConfiguration(\XLite\Core\Request::getInstance()->deploy_configuration);

        if ($errorMsg) {
            \XLite\Core\TopMessage::addError($errorMsg);

        } else {
            \XLite\Core\TopMessage::addInfo('Configuration has been successfully deployed');

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'xpc',
                    '',
                    array(
                        'page'  => $this->settings->getPage('PAGE_PAYMENT_METHODS'),
                    )
                )
            );

            $this->doRedirect();

        }
    }

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        \XLite\Core\Config::updateInstance();

        $settings = $this->settings;

        $connectResult = $settings->testConnection(false);

        if ($settings::RESULT_FAILED !== $connectResult) {
            $settings->importPaymentMethods($connectResult);
        }

        $page = $settings->isPageValid(\XLite\Core\Request::getInstance()->page)
            ? \XLite\Core\Request::getInstance()->page
            : $settings->getDefaultPage();

        $this->setReturnURL(
            $this->buildURL(
                'xpc',
                null,
                array('page' => $page)
            )
        );
    }

    /**
     * Request and import payment configurations
     *
     * @return void
     */
    protected function doActionImport()
    {
        $settings = $this->settings;

        $connectResult = $settings->testConnection(false);

        if ($settings::RESULT_FAILED !== $connectResult) {
            $settings->importPaymentMethods($connectResult);
        }
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\Module\CDev\XPaymentsConnector\View\Model\Settings';
    }

    /**
     * Get pages pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        $list += $this->settings->getAllPages();

        if ($this->isConfigured()) {
        
            unset($list[$this->settings->getPage('PAGE_WELCOME')]);
        }

        return $list;
    }

    

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        foreach ($this->settings->getAllPages() as $page => $title) {
            $list[$page] = 'modules/CDev/XPaymentsConnector/settings/settings.tpl';
        }    

        if ($this->isConfigured()) {

            unset($list[$this->settings->getPage('PAGE_WELCOME')]);
        }

        return $list;
    }

}
