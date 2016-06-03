<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step;

/**
 * AStep
 */
abstract class AStep extends \XLite\View\Upgrade\AUpgrade
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/step';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.step';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !isset(\XLite\Core\Request::getInstance()->mode);
    }

    /**
     * Get an action URL
     *
     * @return string
     */
    protected function getCurrentSnapshotURL()
    {
        return \Includes\SafeMode::getLatestSnapshotURL();
    }

    /**
     * is current snapshot available
     *
     * @return boolean
     */
    public function isCurrentSnapshotAvailable()
    {
        return (boolean) \Includes\SafeMode::getLatestSnapshot();
    }

    /**
     * Get an action URL
     *
     * @return string
     */
    protected function getSoftResetURL()
    {
        return \Includes\SafeMode::getResetURL(true);
    }

    /**
     * Get an action URL
     *
     * @return string
     */
    protected function getHardResetURL()
    {
        return \Includes\SafeMode::getResetURL(false);
    }

    /**
     * Return wrong permissions
     *
     * @return array
     */
    protected function getWrongPermissions()
    {
        return array();
    }
}
