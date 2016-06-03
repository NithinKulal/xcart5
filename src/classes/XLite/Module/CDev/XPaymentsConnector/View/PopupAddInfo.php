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

namespace XLite\Module\CDev\XPaymentsConnector\View;

/**
 * Popup payment additional info 
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupAddInfo extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('popup_add_info'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/XPaymentsConnector/order/add_info';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.tpl';
    }

    public function getTime($time) 
    {
        return \XLite\Core\Converter::getInstance()->formatTime($time);
    }


    /**
     * Get X-Payments connector transactions
     *
     * @return boolean
     */
    public function getXpcTransactionsAddInfo()
    {
        $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find(
            \XLite\Core\Request::getInstance()->transaction_id
        );

        $result = false;

        if (
            $transaction
            && $transaction->isXpc(true)
            && $transaction->getDataCell('xpc_txnid')
        ) {

            $client = \XLite\Module\CDev\XPaymentsConnector\Core\XPaymentsClient::getInstance();

            $info = $client->requestPaymentAdditionalInfo($transaction->getDataCell('xpc_txnid')->getValue());

            if ($info->isSuccess()) {

                $response = $info->getResponse();
                if (
                    !empty($response['transactions'])
                    && is_array($response['transactions'])
                ) {
                    $result = $response['transactions'];
                }
            }
        }

        return $result;
    }
}
