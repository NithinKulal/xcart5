<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Enter license key popup text
 *
 * @ListChild (list="install-modules.pager.buttons", weight="200", zone="admin")
 */
class EnterLicenseKey extends \XLite\View\Button\APopupButton
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/enter_license_key.js';

        return $list;
    }

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
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'module_key',
            'action' => 'view',
            'widget' => '\XLite\View\ModulesManager\AddonKey',
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' enter-license-key';
    }

    /**
     * Button is visible only if license has been activated
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite::getXCNLicense();
    }
}
