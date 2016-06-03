<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * UpgradeNotes
 */
class UpgradeNotes extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    /**
     * Upgrade notes
     *
     * @var array
     */
    protected $upgradeNotes = null;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/style.css';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/upgrade_notes';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.upgrade_notes';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool) $this->getUpgradeNotes();
    }

    /**
     * Return list of files
     *
     * @return array
     */
    protected function getUpgradeNotes()
    {
        if (!isset($this->upgradeNotes)) {
            /** @var \XLite\Upgrade\Entry\AEntry $entity */
            foreach ($this->getUpgradeEntries() as $entity) {
                $notes = $entity->getUpgradeNotes('pre_upgrade');

                if ($notes) {
                    $this->upgradeNotes[$entity->getName()] = $notes;
                }
            };
        }

        return $this->upgradeNotes;
    }
}
