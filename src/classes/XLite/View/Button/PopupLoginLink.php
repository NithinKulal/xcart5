<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;


/**
 * Login form in popup
 */
class PopupLoginLink extends \XLite\View\Button\PopupLogin
{
    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Sign in';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-button popup-login';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }
}
