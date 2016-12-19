<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Processor;

/**
 * 'PhoneOrdering' payment processor
 * We need this class only because we need to make this payment method unremovable
 */
class PhoneOrdering extends \XLite\Model\Payment\Processor\Offline
{

    /**
     * @inheritdoc
     */
    public function isForcedEnabled(\XLite\Model\Payment\Method $method)
    {
        return parent::isForcedEnabled($method)
            || \XLite\Core\Auth::getInstance()->isOperatingAsUserMode();
    }

    /**
     * @inheritdoc
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return parent::isApplicable($order, $method)
            || \XLite\Core\Auth::getInstance()->isOperatingAsUserMode();
    }
}
