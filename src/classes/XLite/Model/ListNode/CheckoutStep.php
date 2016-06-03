<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\ListNode;

/**
 * Checkout step
 */
class CheckoutStep extends \XLite\Model\ListNode
{
    /**
     * Is checkout step passed or not
     *
     * @var boolean
     */
    protected $isPassed = false;

    /**
     * Name of the widget class for this checkout step
     *
     * @var string
     */
    protected $widgetClass = null;


    /**
     * __construct
     *
     * @param string  $key         Step mode
     * @param string  $widgetClass Step widget class name
     * @param boolean $isPassed    If step is passed or not
     *
     * @return void
     */
    public function __construct($key, $widgetClass, $isPassed)
    {
        parent::__construct($key);

        $this->isPassed    = $isPassed;
        $this->widgetClass = $widgetClass;
    }

    /**
     * isPassed
     *
     * @return boolean
     */
    public function isPassed()
    {
        return $this->isPassed;
    }

    /**
     * checkMode
     *
     * @param string $mode Current mode
     *
     * @return boolean
     */
    public function checkMode($mode)
    {
        return isset($mode) ? $this->checkKey($mode) : $this->isPassed();
    }

    /**
     * getWidgetClass
     *
     * @return string
     */
    public function getWidgetClass()
    {
        return $this->widgetClass;
    }

    /**
     * isRegularStep
     *
     * @return boolean
     */
    public function isRegularStep()
    {
        return call_user_func(array($this->getWidgetClass(), 'isRegularStep'));
    }

    /**
     * getMode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getKey();
    }

    /**
     * getTopMessage
     *
     * @return array
     */
    public function getTopMessage()
    {
        return \XLite\Model\Factory::create($this->getWidgetClass())->getTopMessage($this->isPassed());
    }
}
