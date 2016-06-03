<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core;

/**
 * X-Cart core events.
 */
class Events
{
    /**
     * The WIDGET_BEFORE_RENDER event is triggered before the widget has started rendering.
     */
    const WIDGET_BEFORE_RENDER = 'core.widget.before_render';

    /**
     * The WIDGET_AFTER_RENDER event is triggered immediately after the widget has finished rendering.
     * Event arguments contain information about widget state: visibility / cache status.
     */
    const WIDGET_AFTER_RENDER = 'core.widget.after_render';
}