<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ESelectHPP;

/**
 * Class represents an order
 */
class XLite extends \XLite implements \XLite\Base\IDecorator
{
    /**
     * Dispatch request
     *
     * @return string
     */
    protected static function dispatchRequest()
    {
        $result = parent::dispatchRequest();
        if (
            strlen(\XLite\Core\Request::getInstance()->response_order_id) > 2
            && isset(\XLite\Core\Request::getInstance()->response_order_id)
            && isset(\XLite\Core\Request::getInstance()->result)
            && isset(\XLite\Core\Request::getInstance()->trans_name)
            && isset(\XLite\Core\Request::getInstance()->cardholder)
            && isset(\XLite\Core\Request::getInstance()->message)
        ) {
            $result = 'payment_return';
        }

        return $result;
    }
}
