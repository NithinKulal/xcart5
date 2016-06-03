<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Switch state button
 */
class SwitchState extends \XLite\View\Button\Icon
{
    /**
     * Widget parameter names
     */
    const PARAM_ENABLED = 'enabled';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ENABLED => new \XLite\Model\WidgetParam\TypeBool('Enabled', true),
        );
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function  getClass()
    {
        return trim(
            parent::getClass()
                . ' switch-state '
                . ($this->getParam(self::PARAM_ENABLED) ? 'enabled-state' : 'disabled-state')
        );
    }
}
