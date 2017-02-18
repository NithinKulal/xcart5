<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Model;

class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    /**
     * Return list of available payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = parent::getPaymentMethods();

        return array_filter($list, function ($item) {
            /** @var \XLite\Model\Payment\Method $item */
            return $item->getServiceName() !== 'PayWithAmazon';
        });
    }
}
