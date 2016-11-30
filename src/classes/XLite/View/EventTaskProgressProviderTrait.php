<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Provides data for EventTaskProgress widget
 */
trait EventTaskProgressProviderTrait
{
    /**
     * Returns processor instance
     *
     * @return mixed
     */
    abstract protected function getProcessor();

    /**
     * Get time label 
     * 
     * @return string
     */
    protected function getTimeLabel()
    {
        return \XLite\Core\Translation::formatTimePeriod($this->getProcessor()->getTimeRemain());
    }

    /**
     * Provides status message for progress bar
     * 
     * @return string
     */
    protected function getProgressMessage()
    {
        if ($this->getTimeLabel()) {
            return \XLite\Core\Translation::lbl('About X remaining', array('time' => $this->getTimeLabel()) );
        } else {
            return \XLite\Core\Translation::lbl('Performing task...');
        }
    }

    /**
     * Check - current event driver is blocking or not
     * 
     * @return boolean
     */
    protected function isBlocking()
    {
        return \XLite\Core\EventTask::getInstance()->getDriver()->isBlocking();
    }

    /**
     * Get event task name
     *
     * @return string
     */
    protected function getEventName()
    {
        return $this->getProcessor()->getEventName();
    }
}
