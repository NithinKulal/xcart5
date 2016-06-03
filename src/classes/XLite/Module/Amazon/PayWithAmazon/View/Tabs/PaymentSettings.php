<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Tabs;

/**
 * Tabs related to payment settings
 */
abstract class PaymentSettings extends \XLite\View\Tabs\PaymentSettings implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'pay_with_amazon';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $list['pay_with_amazon'] = [
            'weight' => 300,
            'title'  => static::t('Pay with Amazon'),
            'widget' => 'XLite\View\Model\Settings',
        ];

        return $list;
    }
}
