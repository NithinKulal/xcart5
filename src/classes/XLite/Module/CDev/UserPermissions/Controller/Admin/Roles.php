<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\Controller\Admin;

/**
 * Roles 
 */
class Roles extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Roles');
    }
    
    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\CDev\UserPermissions\View\ItemsList\Model\Roles();
        $list->processQuick();
    }
}

