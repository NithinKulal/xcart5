<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * Premium license modules
 *
 * @ListChild (list="admin.center", weight="1", zone="admin")
 */
class PremiumLicenseModules extends \XLite\View\Upgrade\Step\Prepare\APrepare
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/premium_license_modules';
    }

    /**
     * Return list of premium license modules
     *
     * @return array
     */
    protected function getModules()
    {
        return \XLite\Upgrade\Cell::getInstance()->getPremiumLicenseModules();
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) $this->getModules();
    }
}
