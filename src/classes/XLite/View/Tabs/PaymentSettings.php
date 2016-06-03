<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to payment settings
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PaymentSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'payment_settings';
        $list[] = 'payment_appearance';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'payment_settings' => [
                'weight'   => 100,
                'title'    => static::t('Configuration'),
                'template' => 'payment/configuration.twig',
            ],
            'payment_appearance' => [
                'weight'   => 200,
                'title'    => static::t('Sorting & Descriptions'),
                'widget'    => '\XLite\View\ItemsList\Model\Payment\Methods',
            ],
        ];
    }
}
