<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Activate license key page controller
 */
class ActivateKey extends \XLite\Controller\Admin\ModuleKey
{
    /**
     * Return page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isModuleActivation()
            ? static::t('Activate your X-Cart installation first')
            : static::t('License key registration');
    }

    /**
     * Check if module activation
     *
     * @return boolean
     */
    protected function isModuleActivation()
    {
        return (bool) \XLite\Core\Request::getInstance()->isModule;
    }
}
