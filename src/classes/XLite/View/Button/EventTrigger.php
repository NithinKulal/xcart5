<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Button that triggers defined core event on click
 */
class EventTrigger extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    const PARAM_EVENT        = 'event';

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
        );
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
        return 'btn-event-trigger';
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

        $list['onclick'] = 'javascript: ' . $this->getJSCode();

        return $list;
    }
}
