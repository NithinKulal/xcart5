<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Product\Admin;

/**
 * Abstract product panel for admin interface
 */
abstract class AAdmin extends \XLite\View\StickyPanel\Product\AProduct
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Delete',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\View\Button\DeleteSelectedProducts'
        );

        $list[] = $this->getWidget(
            array(),
            'XLite\View\Button\Divider'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Clone',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-copy',
            ),
            'XLite\View\Button\CloneSelected'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Enable',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-power-off state-on',
            ),
            'XLite\View\Button\EnableSelected'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Disable',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-power-off state-off',
            ),
            'XLite\View\Button\DisableSelected'
        );

        return $list;
    }


    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            array(),
            'XLite\View\Button\ItemsExport\Product'
        );
        return $list;
    }
}
