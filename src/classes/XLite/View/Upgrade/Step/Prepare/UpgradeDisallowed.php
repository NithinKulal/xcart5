<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * 'Upgrade not allowed' widget
 *
 * @ListChild (list="admin.center", weight="1", zone="admin")
 */
class UpgradeDisallowed extends \XLite\View\Upgrade\Step\Prepare\APrepare
{
    /**
     * Get directory where template is located (body.tpl)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/upgrade_disallowed';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isValidRequirements()
            && $this->getRequirementErrors();
    }

    /**
     * Get text for section header
     *
     * @return string
     */
    protected function getSectionHeadMessage()
    {
        return static::t('Upgrade not allowed');
    }

    /**
     * Get section description
     *
     * @return string
     */
    protected function getSectionDescription()
    {
        return implode('<br />'. PHP_EOL, $this->getRequirementErrors());
    }
}
