<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\Button;

/**
 * Edit all dropdown button
 */
class EditUpsellingProducts extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Edit all');
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $result = [
            'delete'         => [
                'class'    => 'XLite\Module\XC\Upselling\View\Button\UpsellingActions\Delete',
                'params'   => [
                    'label'      => static::t('Delete'),
                    'style'      => 'more-action link list-action delete-relations',
                    'icon-style' => 'fa fa-times',
                ],
                'position' => 0,
            ],
            'enable'         => [
                'class'    => 'XLite\Module\XC\Upselling\View\Button\UpsellingActions\EnableMutual',
                'params'   => [
                    'style'      => 'more-action link list-action enable-mutual',
                    'icon-style' => 'fa fa-power-off',
                ],
                'position' => 0,
            ],
            'disable'         => [
                'class'    => 'XLite\Module\XC\Upselling\View\Button\UpsellingActions\DisableMutual',
                'params'   => [
                    'style'      => 'more-action link list-action disable-mutual',
                    'icon-style' => 'fa fa-power-off',
                ],
                'position' => 0,
            ],
        ];

        return $result;
    }
}