<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * ModifiedFiles
 */
class ModifiedFiles extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{

    /**
     * Register JS script to use
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/script.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/modified_files';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.modified_files';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) array_filter($this->getCustomFiles());
    }

    /**
     * Return list of files
     *
     * @return array
     */
    protected function getCustomFiles()
    {
        return \XLite\Upgrade\Cell::getInstance()->getCustomFiles();
    }
}
