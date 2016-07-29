<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
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
        return $this->getDir() . '/body.twig';
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
