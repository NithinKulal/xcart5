<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Profile;

/**
 * \XLite\View\Form\Profile\Main
 */
class ForceChangePassword extends \XLite\View\Form\Profile\Main
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'force_change_password';
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . (\XLite::isAdminZone()?:' use-inline-error'));
    }
}
