<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to shipping
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Shipping extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'shipping_methods';
        $list[] = 'origin_address';
        $list[] = 'automate_shipping_returns';
        $list[] = 'automate_shipping_routine';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'shipping_methods' => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget'    => 'XLite\View\ItemsList\Model\Shipping\Carriers',
            ],
            'origin_address' => [
                'weight' => 200,
                'title'  => static::t('Origin address'),
                'widget' => 'XLite\View\Page\OriginAddress',
            ],
            'automate_shipping_returns' => [
                'weight' => 300,
                'title'  => static::t('Automate Shipping returns'),
                'widget' => 'XLite\View\AutomateShippingReturns',
            ],
            'automate_shipping_routine' => [
                'weight' => 400,
                'title'  => static::t('More shipping solutions'),
                'widget' => 'XLite\View\AutomateShippingRoutine',
            ],
        ];
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !(\XLite::getController() instanceof \XLite\Controller\Admin\ShippingMethods && $this->getMethod());
    }
}
