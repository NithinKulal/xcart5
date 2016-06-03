<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * Buttons
 *
 * @ListChild (list="admin.center", weight="100", zone="admin")
 */
class Buttons extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/buttons';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.buttons';
    }

    /**
     * Add some JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'upgrade/step/ready_to_install/buttons/controller.js';

        return $list;
    }
}
