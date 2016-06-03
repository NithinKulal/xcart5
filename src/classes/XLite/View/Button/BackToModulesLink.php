<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Link as link
 */
class BackToModulesLink extends \XLite\View\Button\SimpleLink
{
    /**
     * Widget params
     */
    const PARAM_MODULE_ID = 'moduleId';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_MODULE_ID => new \XLite\Model\WidgetParam\TypeString('Module ID', ''),
        );
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');
        $module = $repo->find($this->getParam(static::PARAM_MODULE_ID));

        return $module ? $module->getInstalledURL() : '';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Back to modules';
    }
}
