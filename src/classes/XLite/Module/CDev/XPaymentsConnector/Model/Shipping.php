<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model;

/**
 * XPayments payment processor
 *
 */
class Shipping extends \XLite\Model\Shipping implements \XLite\Base\IDecorator
{

    /**
     * Set $ignoreLongCalculations variable
     *
     * @param boolean $value Mode
     *
     * @return void
     */
    public static function setIgnoreLongCalculationsMode($value)
    {
        $forIframe = \XLite\Module\CDev\XPaymentsConnector\Core\Iframe::getInstance()->useIframe()
            && \XLite\Core\Request::getInstance()->xpc_iframe;

        $forCallback = 'check_cart' == \XLite\Core\Request::getInstance()->action;

        if (
            true == $value
            && (
                $forIframe
                || $forCallback
            )
        ) {
            // Workaround for checkCheckoutAction() method of controller.
            // Otherwise shippings might be not calculated
            $value = false;
        }

        parent::setIgnoreLongCalculationsMode($value);
    }
}
