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
class CloneFake extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'clone' => [
                'params'   => [
                    'action'     => 'clone',
                    'label'      => 'Clone selected',
                    'style'      => 'action link list-action',
                ],
                'position' => 100,
            ],
        ];
    }
}
