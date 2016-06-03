<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Link as button
 */
class Link extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    const PARAM_LOCATION = 'location';
    const PARAM_JS_CODE  = 'jsCode';
    const PARAM_BLANK    = 'blank';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_LOCATION => new \XLite\Model\WidgetParam\TypeString('Redirect to', $this->getDefaultLocation(), true),
            self::PARAM_JS_CODE  => new \XLite\Model\WidgetParam\TypeString('JS code', null, true),
            self::PARAM_BLANK    => new \XLite\Model\WidgetParam\TypeBool('Open in new window', false),
        );
    }

    /**
     * JavaScript: this code will be used by default
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return $this->getParam(self::PARAM_BLANK)
            ? 'window.open(\'' . $this->getLocationURL() . '\');'
            : 'self.location = \'' . $this->getLocationURL() . '\';';
    }

    /**
     * Defines the default location path
     *
     * @return null|string
     */
    protected function getDefaultLocation()
    {
        return null;
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return \XLite::getInstance()->getShopURL($this->getParam(static::PARAM_LOCATION));
    }

    /**
     * JavaScript: return specified (or default) JS code to execute
     *
     * @return string
     */
    protected function getJSCode()
    {
        return $this->getParam(self::PARAM_JS_CODE) ?: $this->getDefaultJSCode();
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        if (isset($list['disabled'])) {
            // Remove attribute 'disabled' as it is unallowed for <a> tag
            // Instead the class name 'disabled' will be used (see AButton::getClass() method) and
            // disabling js code in button.js
            unset($list['disabled']);
        }

        return array_merge($list, $this->getLinkAttributes());
    }

    /**
     * Onclick specific attribute is added
     *
     * @return array
     */
    protected function getLinkAttributes()
    {
        return array(
            'onclick' => 'javascript: ' . $this->getJSCode(),
        );
    }
}
