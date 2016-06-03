<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Event;


use Symfony\Component\EventDispatcher\Event;

class WidgetBeforeRenderEvent extends Event
{
    /**
     * @var \XLite\View\AView
     */
    private $widget;

    public function __construct(\XLite\View\AView $widget)
    {
        $this->widget   = $widget;
    }

    /**
     * @return \XLite\View\AView
     */
    public function getWidget()
    {
        return $this->widget;
    }
}