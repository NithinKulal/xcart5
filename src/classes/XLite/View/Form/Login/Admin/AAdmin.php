<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Login\Admin;

/**
 * Abstract log-in form in admn interface
 */
abstract class AAdmin extends \XLite\View\Form\Login\ALogin
{
    /**
     * getSecuritySetting
     *
     * @return boolean
     */
    protected function getSecuritySetting()
    {
        return \XLite\Core\Request::getInstance()->isHTTPS()
            || \XLite\Core\Config::getInstance()->Security->admin_security;
    }
}
