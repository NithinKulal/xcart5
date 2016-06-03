<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

/**
 * Warning
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="10")
 */
class Warning extends \XLite\View\Dialog
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/WebmasterKit/warning';
    }

    /**
     * Returns warning message
     *
     * @return string
     */
    protected function getMessage()
    {
        return static::t(
            'If the store is being run in production, it is strongly recommended NOT to keep the module Webmaster Kit enabled',
            array('url' => $this->getURL())
        );
    }

    /**
     * Returns webmaster kit module url
     *
     * @return string
     */
    protected function getURL()
    {
        /** @var \XLite\Model\Module $module */
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneByModuleName('XC\WebmasterKit');

        return $module->getInstalledURL();
    }

    /**
     * Change visible condition, so visible only for root admin
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !\Includes\Utils\ConfigParser::getOptions(array('performance', 'developer_mode'))
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }
}
