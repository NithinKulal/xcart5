<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Remove button
 */
class SwitchVisibility extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    const PARAM_STATE = 'state';

    /**
     * Get a list of css files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'button/css/switch-visibility.css';

        return $list;
    }


    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/switch-visibility.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/switch-visibility.twig';
    }

    /**
     * Returns widget state
     *
     * @return int
     */
    protected function isEnabled()
    {
        return (int) $this->getParam(self::PARAM_STATE);
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getStyle()
    {
        return 'switch'
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '');
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
            self::PARAM_STATE => new \XLite\Model\WidgetParam\TypeBool('Visible', true),
        );
    }
}
