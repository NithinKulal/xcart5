<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to shipping settings
 *
 * @ListChild (list="add_shipping", zone="admin", weight="10")
 */
class ShippingType extends \XLite\View\Tabs\AJsTabs
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return array(
            'carrier_calculated' => array(
                'weight'   => 100,
                'title'    => 'Carrier-calculated rates',
                'template' => 'shipping/add_method/parts/online_carrier_list.twig',
            ),
            'custom_table' => array(
                'weight'   => 200,
                'title'    => 'Custom table rates',
                'template' => 'shipping/add_method/parts/offline_create.twig',
            ),
        );
    }

    /**
     * Offline help template
     *
     * @return string
     */
    protected function getOfflineHelpTemplate()
    {
        return 'shipping/add_method/parts/offline_help.twig';
    }

    /**
     * Online help template
     *
     * @return string
     */
    protected function getOnlineHelpTemplate()
    {
        return 'shipping/add_method/parts/online_help.twig';
    }
}
