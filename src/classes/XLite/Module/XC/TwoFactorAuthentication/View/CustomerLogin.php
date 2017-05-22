<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View;

/**
 * Authy customer login widget
 *
 * @ListChild (list="center", zone="customer")
 */
class CustomerLogin extends \XLite\View\AView
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
     * Return widget directory
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/TwoFactorAuthentication/login/body.twig';
    }


}