<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Addons search and installation widget
 */
class AModulesManager extends \XLite\View\Dialog
{
    /**
     * Return module identificator
     *
     * @return integer
     */
    protected function getModuleId()
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    /**
     * Return templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules_manager';
    }
}
