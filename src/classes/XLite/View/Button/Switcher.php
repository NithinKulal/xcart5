<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Switcher button
 */
class Switcher extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    const PARAM_ENABLED = 'enabled';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/switcher.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/switcher.twig';
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
            self::PARAM_ENABLED => new \XLite\Model\WidgetParam\TypeBool('Enabled', true),
        );
    }

    /**
     * Get formatted enabled status
     * 
     * @return string
     */
    protected function getEnabled()
    {
        return $this->getParam(self::PARAM_ENABLED) ? '1' : '';
    }

    /**
     * Get style 
     * 
     * @return string
     */
    protected function  getStyle()
    {
        return 'switcher '
            . ($this->getParam(self::PARAM_ENABLED) ? 'on' : 'off')
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '');
    }

    /**
     * Get title 
     * 
     * @return string
     */
    protected function getTitle()
    {
        return $this->getParam(self::PARAM_ENABLED) ? 'Disable' : 'Enable';
    }
}
