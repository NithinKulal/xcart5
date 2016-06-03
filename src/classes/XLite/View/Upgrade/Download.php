<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * Download
 */
class Download extends \XLite\View\Upgrade\AUpgrade
{
    /**
     * Get directory where template is located (body.twig)
     *
     * :TODO: remove if it's not needed
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . LC_DS . 'download';
    }

    /**
     * Return internal list name
     *
     * :TODO: remove if it's not needed
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.select_core_version';
    }

    /**
     * Check if widget is visible
     *
     * :TODO: remove if it's not needed
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isDownload();
    }
}
