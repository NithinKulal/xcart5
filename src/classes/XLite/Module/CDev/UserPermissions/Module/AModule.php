<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\UserPermissions\Module;

/**
 * Module
 */
abstract class AModule extends \XLite\Module\AModule implements \XLite\Base\IDecorator
{
    /**
     * One entry permission registration
     * It is called in static::registerPermissions() method
     *
     * @param string $permissionCode
     * @param string $permissionName
     */
    protected static function registerPermission($permissionCode, $permissionName)
    {
        parent::registerPermission($permissionCode, $permissionName);

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Role\Permission');
        $permission = $repo->findOneByCode($permissionCode);

        return $permission ? $repo->update($permission, array('enabled' => true), false) : false;
    }

    /**
     * One entry permission unregistration
     * It is called in static::unregisterPermissions() method
     *
     * @param string $permissionCode
     */
    protected static function unregisterPermission($permissionCode)
    {
        parent::unregisterPermission($permissionCode);

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Role\Permission');
        $permission = $repo->findOneByCode($permissionCode);

        return $permission ? $repo->update($permission, array('enabled' => false), false) : false;
    }
}
