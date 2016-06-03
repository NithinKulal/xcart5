<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\Template
 */
class Template extends \XLite\View\FormField\Select\Template implements \XLite\Base\IDecorator
{
    /**
     * Check module is selected
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isModuleSelected($module)
    {
        $value = $this->getValue();

        if (static::SKIN_STANDARD === $module) {
            $result = (string) $this->getColorSchemesModuleId() === $value;

        } else {
            $result = $this->getModuleId($module) === $value;
        }

        return $result;
    }

    /**
     * Check if redeploy is required
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function isRedeployRequired($module)
    {
        $value = $this->getValue();

        if ($module === static::SKIN_STANDARD
            || ($value === static::SKIN_STANDARD
                && isset($module['module'])
                && $module['module']->getModuleId() === $this->getColorSchemesModuleId()
            )
        ) {
            $result = false;

        } else {
            $result = parent::isRedeployRequired($module);
        }

        return $result;
    }

    /**
     * Returns ColorSchemes modules id
     *
     * @return integer
     */
    protected function getColorSchemesModuleId()
    {
        static $moduleId = null;

        if (null === $moduleId) {
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')
                ->findOneByModuleName('XC\ColorSchemes');

            $moduleId = $module->getModuleId();
        }

        return $moduleId;
    }
}
