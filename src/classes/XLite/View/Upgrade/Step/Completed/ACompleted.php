<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Completed;

/**
 * ACompleted
 */
abstract class ACompleted extends \XLite\View\Upgrade\Step\AStep
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::getDir() . '/css/style.css';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/completed';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.completed';
    }

    /**
     * Completed steps for upgrade is visible:
     * if there is at least one upgrade entry (core or module) (\XLite\View\Upgrade\EmptyCells widget is displayed instead)
     * and if the upgrade process is finished
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Upgrade\Cell::getInstance()->getEntries()
            && \XLite\Upgrade\Cell::getInstance()->isUpgraded();
    }
}
