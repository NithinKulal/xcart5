<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\State\Admin;

/**
 * Abstract state panel for admin interface
 */
abstract class AAdmin extends \XLite\View\StickyPanel\State\AState
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
                    'action'     => 'delete',
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action hide-on-disable hidden',
                    'icon-style' => 'fa fa-trash-o',
                ],
                'position' => 100,
            ],
        ];
    }
}
