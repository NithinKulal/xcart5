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
        return [
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
            'status' => [
                'class'    => 'XLite\View\Button\Dropdown\Status',
                'params'   => [
                    'label'         => '',
                    'style'         => 'more-action icon-only hide-on-disable hidden',
                    'icon-style'    => 'fa fa-power-off',
                    'showCaret'     => false,
                    'dropDirection' => 'dropup',
                ],
                'position' => 200,
            ],
            'clone' => [
                'class'    => 'XLite\View\Button\Regular',
                'params'   => [
                    'action'     => 'clone',
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-copy',
                    'attributes'    => [
                        'title' => static::t('Clone')
                    ]
                ],
                'position' => 300,
            ],
        ];
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
            [],
            'XLite\View\Button\ItemsExport\Product'
        );

        return $list;
    }
}
