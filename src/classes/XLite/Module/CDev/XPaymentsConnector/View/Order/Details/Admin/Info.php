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

namespace XLite\Module\CDev\XPaymentsConnector\View\Order\Details\Admin;

/**
 * Order info 
 */
class Info extends \XLite\View\Order\Details\Admin\Info implements \XLite\Base\IDecorator
{
    /**
     * Get skin directory
     *
     * @return string
     */
    protected static function getDirectory()
    {
        return 'modules/CDev/XPaymentsConnector/order';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = self::getDirectory() . '/style.css';
        $list[] = self::getDirectory() . '/add_info/style.css';
        $list[] = self::getDirectory() . '/saved_cards/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = self::getDirectory() . '/script.js';

        return $list;
    }

    /**
     * Javascript code for recharge popup 
     *
     * @return string 
     */
    protected function getRechargeJsCode()
    {
        $orderNumber = '\'' . $this->getOrder()->getOrderNumber() . '\'';
        $amount = '\'' . $this->getOrder()->getAomTotalDifference() . '\'';

        $code = 'showRechargeBox(' . $orderNumber . ', ' . $amount . ');';

        return $code;
    }

    /**
     * Is recharge allowed for the order
     *
     * @return boolean
     */
    protected function isAllowRecharge()
    {
        return $this->getOrder()->isAllowRecharge();
    }

    /**
     * Get Kount data
     *
     * @return object
     */
    protected function getKountData()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountData($this->getOrder());
    }

    /**
     * Get list of Kount errors
     *
     * @return array
     */
    protected function getKountErrors()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountErrors($this->getOrder());
    }

    /**
     * Get list of Kount triggered rules
     *
     * @return array
     */
    protected function getKountRules()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountRules($this->getOrder());
    }

    /**
     * Get Kount result as text
     *
     * @return string
     */
    protected function getKountMessage()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountMessage($this->getOrder());
    }

    /**
     * Get Kount transaction ID
     *
     * @return string
     */
    protected function getKountTransactionId()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountTransactionId($this->getOrder());
    }

    /**
     * Get Kount score
     *
     * @return string
     */
    protected function getKountScore()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountScore($this->getOrder());
    }

    /**
     * Get CSS class for Kount score
     *
     * @return string
     */
    protected function getKountScoreClass()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountScoreClass($this->getOrder());
    }

    /**
     * Check - display AntiFraud module advertisment or not
     *
     * @return boolean
     */
    protected function isDisplayAntiFraudAd()
    {
        $result = parent::isDisplayAntiFraudAd();

        if (
            $result
            && \XLite\Module\CDev\XPaymentsConnector\Core\Kount::getInstance()->getKountData($this->getOrder())
        ) {
            $result = false;
        }

        return $result;
    }
}
