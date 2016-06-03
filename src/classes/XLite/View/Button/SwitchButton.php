<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Switch button (register two onclick callbacks JS functions)
 */
class SwitchButton extends \XLite\View\Button\AButton
{
    /**
     * Several inner constants
     */
    const JS_SCRIPT = 'button/js/switch-button.js';
    const SWITCH_CSS_FILE = 'button/css/switch-button.css';

    /**
     * Widget parameters to use
     */
    const PARAM_FIRST  = 'first';
    const PARAM_SECOND = 'second';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::JS_SCRIPT;

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::SWITCH_CSS_FILE;

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/switch-button.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_FIRST  => new \XLite\Model\WidgetParam\TypeString('First callback', ''),
            self::PARAM_SECOND => new \XLite\Model\WidgetParam\TypeString('Second callback', ''),
        );
    }

    /**
     * Return JS callbacks to use with onclick event
     *
     * @return array
     */
    protected function getCallbacks()
    {
        return array(
            'callbacks' => array (
                'first'  => $this->getParam(self::PARAM_FIRST),
                'second' => $this->getParam(self::PARAM_SECOND),
            ),
        );
    }
}
