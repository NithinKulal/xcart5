<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Backup widget
 */
class Backup extends \XLite\Module\XC\ThemeTweaker\View\Custom
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $backup = $this->getBackupContent();

        return $backup
            && $backup != $this->getFileContent();
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir(). '/backup';
    }
}
