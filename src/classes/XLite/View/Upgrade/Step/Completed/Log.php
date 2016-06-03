<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Completed;

/**
 * Log
 *
 * @ListChild (list="admin.center", weight="400", zone="admin")
 */
class Log extends \XLite\View\Upgrade\Step\Completed\ACompleted
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/log';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.log';
    }

    /**
     * Get the log file link
     *
     * @return string
     */
    protected function getLogFileURL()
    {
        return $this->buildURL('upgrade', 'view_log_file');
    }

    /**
     * Called after the includeCompiledFile()
     *
     * @return void
     */
    protected function closeView()
    {
        parent::closeView();

        \XLite\Upgrade\Cell::getInstance()->clear(true, true, false);
        \XLite\Upgrade\Cell::getInstance()->setUpgraded(false);
    }
}
