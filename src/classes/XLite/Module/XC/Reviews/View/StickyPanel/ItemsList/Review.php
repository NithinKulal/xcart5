<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\StickyPanel\ItemsList;

/**
 * Reviews items list's sticky panel
 */
class Review extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'state'  => [
                'class' => 'XLite\Module\XC\Reviews\View\Button\Admin\ReviewStatus',
                'params' => [
                    'label'         => '',
                    'style'         => 'more-action icon-only hide-on-disable hidden',
                    'icon-style'    => 'fa fa-check',
                    'showCaret'     => false,
                    'dropDirection' => 'dropup',
                ],
                'position' => 100,
            ],
            'delete' => [
                'class'    => 'XLite\View\Button\DeleteSelected',
                'params'   => [
                    'label'      => '',
                    'style'      => 'more-action icon-only hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 200,
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
            'XLite\Module\XC\Reviews\View\Button\ItemsExport\Reviews'
        );

        return $list;
    }
}
