<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\Button;

/**
 * Resend TwoFactorAuthentication SMS button
 */
class ResendAuthy extends \XLite\View\Button\AButton
{
    /**
     * Get list of JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/TwoFactorAuthentication/resend_token.js';

        return $list;
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        $classes = parent::getClass();

        return 'resend_token ' . $classes;
    }
}