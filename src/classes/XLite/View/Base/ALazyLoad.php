<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Base;

/**
 * Lazy load container
 */
abstract class ALazyLoad extends \XLite\View\AView
{
    const PARAM_LAZY_CLASS        = 'lazyClass';
    const PARAM_LAZY_CLASS_PARAMS = 'lazyClassParams';
    const PARAM_LAZY_EVENT        = 'lazyEvent';

    /**
     * Lazy widget
     *
     * @var \XLite\View\AView
     */
    protected $lazyWidget = null;

    /**
     * Register CSS§ files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $lazyWidget = $this->getLazyWidget();

        return array_merge($list, $lazyWidget->getCSSFiles());
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'lazy_load/controller.js';
        $lazyWidget = $this->getLazyWidget();

        return array_merge($list, $lazyWidget->getJSFiles());
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'lazy_load/body.twig';
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
            self::PARAM_LAZY_CLASS
                => new \XLite\Model\WidgetParam\TypeString('Lazy class', $this->getDefaultLazyClass()),
            self::PARAM_LAZY_CLASS_PARAMS
                => new \XLite\Model\WidgetParam\TypeCollection('Lazy class params', $this->getDefaultLazyClassParams()),
            self::PARAM_LAZY_EVENT
                => new \XLite\Model\WidgetParam\TypeString('Lazy event', $this->getDefaultLazyEvent()),
        );
    }

    /**
     * Returns default lazy class
     *
     * @return string
     */
    protected function getDefaultLazyClass()
    {
        return '';
    }

    /**
     * Returns default lazy event (the event triggers node reload)
     *
     * @return string
     */
    protected function getDefaultLazyEvent()
    {
        return array();
    }

    /**
     * Returns default lazy class params
     *
     * @return array
     */
    protected function getDefaultLazyClassParams()
    {
        return array();
    }

    /**
     * Returns lazy class
     *
     * @return string
     */
    protected function getLazyClass()
    {
        return $this->getParam(static::PARAM_LAZY_CLASS);
    }

    /**
     * Returns lazy class params
     *
     * @return string
     */
    protected function getLazyClassParams()
    {
        return $this->getParam(static::PARAM_LAZY_CLASS_PARAMS);
    }

    /**
     * Returns lazy widget
     *
     * @return \XLite\View\AView
     */
    protected function getLazyWidget()
    {
        if (!isset($this->lazyWidget)) {
            $this->lazyWidget = $this->getChildWidget($this->getLazyClass(), $this->getLazyClassParams());
        }

        return $this->lazyWidget;
    }

    /**
     * Check lazy content
     * todo: check for request type (true for ajax)
     *
     * @return string
     */
    protected function hasLazyContent()
    {
        return \XLite\Core\Request::getInstance()->isAJAX()
            ? true
            : $this->getLazyWidget()->hasCachedContent();
    }

    /**
     * Returns lazy content
     *
     * @return string
     */
    protected function getLazyContent()
    {
        return $this->hasLazyContent()
            ? $this->getLazyWidget()->getContent()
            : '';
    }

    /**
     * Returns style classes
     *
     * @return array
     */
    protected function getStyleClasses()
    {
        $styleClasses = array();
        $styleClasses[] = 'lazy-load';

        if (!$this->hasLazyContent()) {
            $styleClasses[] = 'active';
        }

        return $styleClasses;
    }

    /**
     * Returns lazy event.
     * To specify the number of events, list them separated by commas
     *
     * @return string
     */
    protected function getLazyEvents()
    {
        return $this->getParam(static::PARAM_LAZY_EVENT);
    }

    /**
     * Returns attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = array();
        $attributes['class'] = implode(' ', $this->getStyleClasses());
        $attributes['data-lazy-class'] = get_class($this);
        if ($this->getLazyEvents()) {
            $attributes['data-lazy-event'] = implode(',', $this->getLazyEvents());
        }

        return $attributes;
    }
}
