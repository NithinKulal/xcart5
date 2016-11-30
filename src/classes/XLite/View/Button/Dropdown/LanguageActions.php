<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Order print
 */
class LanguageActions extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        return [
            'find' => [
                'class' => 'XLite\View\Button\Link',
                'params' => [
                    'disabled' => false,
                    'label'    => 'Find language in marketplace',
                    'style'    => 'action link always-enabled',
                    'location' => $this->buildURL('addons_list_marketplace', '', array('tag' => 'Translation')),
                ],
                'position' => 100,
            ],
            'import' => [
                'class' => 'XLite\View\Button\FileSelector',
                'params' => [
                    'disabled'   => false,
                    'label'      => 'Import language from CSV file',
                    'style'      => 'action link always-enabled',
                    'object'     => 'language',
                    'fileObject' => 'file',
                ],
                'position' => 200,
            ],
            'add' => [
                'class' => 'XLite\View\LanguagesModify\Button\AddNewLanguage',
                'params' => [
                    'disabled' => false,
                    'label'    => 'Add language',
                    'style'    => 'action link always-enabled',
                ],
                'position' => 300,
            ],
        ];
    }
}
