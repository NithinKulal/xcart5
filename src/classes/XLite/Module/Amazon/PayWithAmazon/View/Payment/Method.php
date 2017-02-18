<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Payment;

use XLite\Module\Amazon\PayWithAmazon\Main;

abstract class Method extends \XLite\View\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'pay_with_amazon';

        return $result;
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return \XLite::getController()->getTarget() === 'pay_with_amazon'
            ? Main::getMethod()
            : parent::getPaymentMethod();
    }
}
