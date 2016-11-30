<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Show modules to install
 */
class InstallModulesSelected extends \XLite\View\Button\Dropdown\ADropdown
{
    /**
     * Return JS files for the widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules_manager/js/install_modules_selected.js';

        return $list;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();
        $position = 0;
        $modules = \XLite::getController()->getModulesToInstall();

        $list['module-empty'] = [
            'params'   => [
                'style'    => 'link list-action modules-not-selected',
                'template' => 'button/addon/install_module_not_selected.twig',
                'disabled' => true,
            ],
            'position' => $position += 100,
        ];

        $list['module-0'] = [
            'params'   => [
                'template' => 'button/addon/install_module_selected.twig',
                'style' => 'module-box clone',
            ],
            'position' => $position += 100,
        ];

        foreach ($modules as $moduleId) {
            $list['module-' . $moduleId] = [
                'params'   => [
                    // 'label' => \XLite::getController()->getModuleName($moduleId),
                    'style'    => 'always-enabled module-box',
                    'template' => 'button/addon/install_module_selected.twig',
                    'moduleId' => $moduleId,
                ],
                'position' => $position += 100,
            ];
        }

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t(
            'X module(s) selected',
            ['count' => \XLite::getController()->countModulesSelected()]
        );
    }
}
