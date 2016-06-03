<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Tooltip button
 */
class Tooltip extends \XLite\View\Button\Regular
{
    /**
     * Widget parameter names
     */
    const PARAM_BUTTON_TOOLTIP = 'buttonTooltip';
    const PARAM_SEPARATE_TOOLTIP = 'tooltip';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/tooltip.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/tooltip.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/tooltip.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_BUTTON_TOOLTIP => new \XLite\Model\WidgetParam\TypeString('Button tooltip', ''),
            static::PARAM_SEPARATE_TOOLTIP => new \XLite\Model\WidgetParam\TypeString('Separate tooltip', ''),
        );
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ($this->getParam(static::PARAM_BUTTON_TOOLTIP) ? ' tooltip-caption' : '');
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'button-tooltip'
            . ($this->getParam(static::PARAM_BUTTON_TOOLTIP) ? ' tooltip-main' : '');
    }

    /**
     * Get button tooltip
     *
     * @return string
     */
    protected function getButtonTooltip()
    {
        return $this->getParam(static::PARAM_BUTTON_TOOLTIP);
    }

    /**
     * Get separate tooltip
     *
     * @return string
     */
    protected function getSeparateTooltip()
    {
        return $this->getParam(static::PARAM_SEPARATE_TOOLTIP);
    }
}
