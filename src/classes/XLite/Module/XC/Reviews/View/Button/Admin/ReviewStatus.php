<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Admin;

/**
 * Review status
 */
class ReviewStatus extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'approve' => [
                'params'   => [
                    'action'     => 'approve',
                    'label'      => 'Approve selected',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-check state-on',
                ],
                'position' => 100,
            ],
            'unapprove' => [
                'params'   => [
                    'action'     => 'unapprove',
                    'label'      => 'Unapprove selected',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-check state-off',
                ],
                'position' => 200,
            ],
        ];
    }
}
