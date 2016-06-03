<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * 'No updates available' widget
 *
 * @ListChild (list="admin.center", weight="100", zone="admin")
 */
class EmptyCells extends \XLite\View\Upgrade\AUpgrade
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->getUpgradeEntries();
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/empty_cells';
    }
}
