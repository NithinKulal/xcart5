<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View;

/**
 * Authy admin login widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AdminLogin extends \XLite\View\Login
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = \XLite\View\AView::getAllowedTargets();

        $result[] = 'authy_login';

        return $result;
    }

    /**
     * Get list of JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/TwoFactorAuthentication/style.less';

        return $list;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/TwoFactorAuthentication/login';
    }

}
