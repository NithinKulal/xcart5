<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

/**
 * 'Install' action link for marketplace (Modules installation)
 */
class SelectToInstall extends \XLite\View\ModulesManager\Action\AAction
{
    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return 'select-to-install-action';
    }

    /**
     * Return JS files for the widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'items_list/module/install/parts/columns/info/actions/js/install.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/install/parts/columns/info/actions/install.twig';
    }
}
