<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Dashboard page widget 
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class SafeMode extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('safe_mode'));
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Get safe mode access key
     *
     * @return string
     */
    public function getSafeModeKey()
    {
        return \Includes\SafeMode::getAccessKey();
    }

    /**
     * Get Hard Reset URL
     *
     * @return string
     */
    public function getHardResetURL()
    {
        return \Includes\SafeMode::getResetURL();
    }

    /**
     * Get Soft Reset URL
     *
     * @return string
     */
    public function getSoftResetURL()
    {
        return \Includes\SafeMode::getResetURL(true);
    }

    /**
     * Get Current Snapshot URL
     *
     * @return string
     */
    public function getCurrentSnapshotURL()
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
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'safe_mode';
    }

}
