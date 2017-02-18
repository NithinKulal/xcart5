<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\StickyPanel\Payment;

/**
 * Payment method settings sticky panel
 */
class Settings extends \XLite\View\StickyPanel\Payment\Settings implements \XLite\Base\IDecorator
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        if (\XLite::getController()->getTarget() === 'pay_with_amazon') {
            unset($list['addons-list']);
        }

        return $list;
    }
}
