<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

/**
 * 'Manage layout' action link for Module list (Modules manage)
 *
 * @ListChild (list="itemsList.module.manage.columns.module-main-section.actions", weight="17", zone="admin")
 */
class ManageLayout extends \XLite\View\ModulesManager\Action\AAction
{
    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return 'manage-layout-action no-disable';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/manage/parts/columns/module-main-section/actions/manage-layout.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModule()->callModuleMethod('isSkinModule')
            && $this->getModule()->canEnable();
    }
}
