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
    const PARAM_BUTTON_TOOLTIP   = 'buttonTooltip';
    const PARAM_KEEP_ON_HOVER    = 'keepOnHover';
    const PARAM_SEPARATE_TOOLTIP = 'tooltip';
    const PARAM_PLACEMENT        = 'placement';
    const PARAM_DELAY            = 'delay';
    const PARAM_DELAY_SHOW       = 'delayShow';
    const PARAM_HELP_ID          = 'helpId';

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

        $this->widgetParams += [
            static::PARAM_BUTTON_TOOLTIP   => new \XLite\Model\WidgetParam\TypeString('Button tooltip', ''),
            static::PARAM_SEPARATE_TOOLTIP => new \XLite\Model\WidgetParam\TypeString('Separate tooltip', ''),
            static::PARAM_KEEP_ON_HOVER    => new \XLite\Model\WidgetParam\TypeBool('Keep on hover', true),
            static::PARAM_PLACEMENT        => new \XLite\Model\WidgetParam\TypeString('Tooltip placement', 'top auto'),
            static::PARAM_DELAY            => new \XLite\Model\WidgetParam\TypeInt('Tooltip hide delay', 0),
            static::PARAM_DELAY_SHOW       => new \XLite\Model\WidgetParam\TypeInt('Tooltip show delay', 0),
            static::PARAM_HELP_ID          => new \XLite\Model\WidgetParam\TypeString('ID of element contaning help text', ''),
        ];
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
     * Keep on hover
     *
     * @return boolean
     */
    protected function isKeepOnHover()
    {
        return $this->getParam(static::PARAM_KEEP_ON_HOVER);
    }

    /**
     * Get trigger
     *
     * @return string
     */
    protected function getTrigger()
    {
        return $this->isKeepOnHover()
            ? 'manual'
            : 'hover';
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

    /**
     * Get ID of element containing help text
     *
     * @return string
     */
    protected function getHelpId()
    {
        return $this->getParam(static::PARAM_HELP_ID);
    }

    /**
     * Get ID of element containing help text
     *
     * @return string
     */
    protected function getDelay()
    {
        $delayShow = (int) $this->getParam(static::PARAM_DELAY_SHOW);
        $delayHide = (int) $this->getParam(static::PARAM_DELAY);

        if ($delayShow == $delayHide) {
            $result = $delayShow;

        } else {
            $result = json_encode(array('show' => $delayShow, 'hide' => $delayHide));
        }

        return $result;
    }
}
