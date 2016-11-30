<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Status
 */
class Status extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'enable' => [
                'params'   => [
                    'action'     => 'enable',
                    'label'      => 'Enable selected',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-power-off state-on',
                ],
                'position' => 100,
            ],
            'disable' => [
                'params'   => [
                    'action'     => 'disable',
                    'label'      => 'Disable selected',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-power-off state-off',
                ],
                'position' => 200,
            ],
        ];
    }
}
