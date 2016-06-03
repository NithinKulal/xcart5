<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ImageResize;

/**
 * Progress section
 */
class Progress extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'image_resize/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'image_resize/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'image_resize/progress.twig';
    }

    /**
     * Get time label
     *
     * @return string
     */
    protected function getTimeLabel()
    {
        return \XLite\Core\Translation::formatTimePeriod($this->getImageResizeGenerator()->getTimeRemain());
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
     * Get export event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return \XLite\Logic\ImageResize\Generator::getEventName();
    }
}
