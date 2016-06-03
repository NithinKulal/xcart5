<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Event;


use Symfony\Component\EventDispatcher\Event;
use XLite\View\AView;

class WidgetAfterRenderEvent extends Event
{
    const STATE_VISIBLE = 1;
    const STATE_CACHED  = 2;

    /**
     * @var AView
     */
    private $widget;

    /**
     * @var int
     */
    private $state;

    /**
     * @var string
     */
    private $template;

    public function __construct(AView $widget, $state = 0, $template)
    {
        $this->widget   = $widget;
        $this->state    = $state;
        $this->template = $template;
    }

    /**
     * @return AView
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * @return boolean
     */
    public function isCached()
    {
        return $this->state & self::STATE_CACHED;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->state & self::STATE_VISIBLE;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}