<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Authorization
 *
 * @ListChild (list="center", zone="customer")
 */
class Authorization extends \XLite\View\SimpleDialog
{
    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return \XLite\Core\Request::getInstance()->popup
            ? 'authorization/authorization_popup.twig'
            : 'authorization/authorization.twig';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'login';

        if ('login' == \XLite\Core\Request::getInstance()->mode) {
            $list[] = 'profile';
        }

        return $list;
    }

    /**
     * Get login
     *
     * @return string
     */
    protected function getLogin()
    {
        return \XLite\Core\Request::getInstance()->login ?: null;
    }
}
