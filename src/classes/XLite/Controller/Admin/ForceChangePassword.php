<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * User profile page controller
 */
class ForceChangePassword extends \XLite\Controller\Admin\Profile
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Return class name of the register form
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Profile\ForceChangePassword';
    }
}
