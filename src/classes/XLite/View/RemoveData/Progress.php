<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\RemoveData;


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
        $list[] = 'remove_data/style.css';

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
        $list[] = 'remove_data/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'remove_data/progress.twig';
    }

    /**
     * Get time label
     *
     * @return string
     */
    protected function getTimeLabel()
    {
        $generator = \XLite\Logic\RemoveData\Generator::getInstance();

        return \XLite\Core\Translation::formatTimePeriod($generator->getTimeRemain());
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
        return \XLite\Logic\RemoveData\Generator::getEventName();
    }
}