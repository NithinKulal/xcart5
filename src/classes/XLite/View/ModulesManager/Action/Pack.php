<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

/**
 * 'Pack it' action link for Module list (Modules manage)
 *
 * @ListChild (list="itemsList.module.manage.columns.module-main-section.actions", weight="30", zone="admin")
 */
class Pack extends \XLite\View\ModulesManager\Action\AAction
{
    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return 'pack-action';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/manage/parts/columns/module-main-section/actions/pack.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isDeveloperMode();
    }
}
