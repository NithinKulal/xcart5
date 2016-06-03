<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Activate license key popup text
 * 
 * @ListChild (list="install-modules.pager.buttons", weight="200", zone="admin")
 */
class ActivateLicenseKey extends \XLite\View\Button\ActivateKey
{
    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Activate license key';
    }

    /**
     * Check if module activation
     *
     * @return boolean
     */
    protected function isModuleActivation()
    {
        return true;
    }

    /**
     * Button is visible only if license was not activated
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getXCNLicense();
    }
}
