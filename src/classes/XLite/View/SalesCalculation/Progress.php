<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SalesCalculation;

/**
 * Progress section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Progress extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('sales_calculation'));
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'sales_calculation/style.css';

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
        $list[] = 'sales_calculation/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'sales_calculation/progress.twig';
    }

    /**
     * Get time label
     *
     * @return string
     */
    protected function getTimeLabel()
    {
        return \XLite\Core\Translation::formatTimePeriod($this->getSalesGenerator()->getTimeRemain());
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
        return \XLite\Logic\Sales\Generator::getEventName();
    }
}
