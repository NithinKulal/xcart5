<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Event task progress bar
 */
class EventTaskProgress extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_EVENT             = 'event';
    const PARAM_MESSAGE           = 'message';
    const PARAM_TITLE             = 'title';
    const PARAM_BLOCKING_NOTE     = 'blockingNote';
    const PARAM_NON_BLOCKING_NOTE = 'nonBlockingNote';
    const PARAM_SHOW_CANCEL       = 'showCancel';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'event_task_progress/controller.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'event_task_progress/style.css';

        return $list;
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
            static::PARAM_EVENT             => new \XLite\Model\WidgetParam\TypeString('Event name', null),
            static::PARAM_MESSAGE           => new \XLite\Model\WidgetParam\TypeString('Event message', ''),
            static::PARAM_TITLE             => new \XLite\Model\WidgetParam\TypeString('Progress bar title', null),
            static::PARAM_BLOCKING_NOTE     => new \XLite\Model\WidgetParam\TypeString('Blocking note', null),
            static::PARAM_NON_BLOCKING_NOTE => new \XLite\Model\WidgetParam\TypeString('Non-blocking note', null),
            static::PARAM_SHOW_CANCEL       => new \XLite\Model\WidgetParam\TypeBool('Show cancel button', true),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {   
        return 'event_task_progress/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getTmpVar();
    }

    /**
     * Get temporary variable data
     * 
     * @return array
     */
    protected function getTmpVar()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getParam(static::PARAM_EVENT));
    }

    // {{{ Content helpers

    /**
     * Get event title 
     * 
     * @return string
     */
    protected function getEventTitle()
    {
        return $this->getParam(static::PARAM_TITLE);
    }

    /**
     * Get event name
     * 
     * @return string
     */
    protected function getEvent()
    {
        return $this->getParam(static::PARAM_EVENT);
    }

    /**
     * Get event name
     * 
     * @return string
     */
    protected function getMessage()
    {
        return $this->getParam(static::PARAM_MESSAGE);
    }

    /**
     * Get percent
     * 
     * @return integer
     */
    protected function getPercent()
    {
        $rec = $this->getTmpVar();

        return 0 < $rec['position'] ? min(100, floor(($rec['position']) / $rec['length'] * 100)) : 0;
    }

    /**
     * Returns css classes for progress bar container
     * @return array
     */
    protected function getCSSClasses()
    {
        return array(
            $this->getControllerClass(),
        );
    }


    /**
     * Returns css classes for progress bar container
     * @return array
     */
    protected function getProgressBarContainerClasses()
    {
        return array(
            'progress-bar-container',
            'progress',
            'active',
            'progress-striped',
            ($this->isBlockingDriver() ? 'blocking' : 'noblocking')
        );
    }

    /**
     * Returns css class used in js-controller
     * @return string
     */
    protected function getControllerClass()
    {
        return 'event-task-progress';
    }

    /**
     * Returns css classes for progress bar
     * @return array
     */
    protected function getProgressBarClasses()
    {
        return array(
            'progress-bar',
        );
    }

    /**
     * Returns css classes for progress bar status message
     * @return array
     */
    protected function getMessageClasses()
    {
        return array(
            'progress-message',
        );
    }

    protected function showCancelButton()
    {
        return $this->getParam(static::PARAM_SHOW_CANCEL);
    }

    /**
     * Check - current event driver is blocking or not
     * 
     * @return boolean
     */
    protected function isBlockingDriver()
    {
        return \XLite\Core\EventTask::getInstance()->getDriver()->isBlocking();
    }

    /**
     * Get blocking note 
     * 
     * @return string
     */
    protected function getBlockingNote()
    {
        return $this->getParam(static::PARAM_BLOCKING_NOTE) ?: static::t('The process of export may take much time. You may close the page, the operation will be in progress as background. If the operation takes long enough, we will send you a notification when it is complete.');
    }

    /**
     * Get non-blocking note
     *
     * @return string
     */
    protected function getNonBlockingNote()
    {
        return $this->getParam(static::PARAM_NON_BLOCKING_NOTE) ?: static::t('This process may take much time. Please do not close this page until the task is complete');
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    protected function getPreloadedLabels()
    {
        $list = array(
            'Canceling',
            'Canceled',
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    // }}}
}
