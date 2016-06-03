<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Button;

/**
 * Regular button
 */
class ListItemAction extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    const PARAM_EVENT        = 'event';
    const PARAM_TOOLTIP      = 'tooltip';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_EVENT     => new \XLite\Model\WidgetParam\TypeString('Event name', '', true),
            self::PARAM_TOOLTIP   => new \XLite\Model\WidgetParam\TypeString('Button tooltip text', '', true),
        );
    }

    /**
     * Returns button tootip text
     *
     * @return string
     */
    protected function getTooltip()
    {
        return static::t($this->getParam(self::PARAM_TOOLTIP));
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return '';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'list-item-action';
    }

    /**
     * Return specified (or default) JS code
     *
     * @return string
     */
    protected function getJSCode()
    {
        $event = $this->getParam(self::PARAM_EVENT);

        return empty($event) ? '' : "core.trigger('$event', this);";
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        if ($this->getTooltip()) {
            $list['title'] = $this->getTooltip();
            $list['data-toggle'] = 'tooltip';
        }

        $list['onclick'] = 'javascript: ' . $this->getJSCode();

        return $list;
    }
}
