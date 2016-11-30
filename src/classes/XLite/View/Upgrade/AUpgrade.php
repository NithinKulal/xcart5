<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * AUpgrade
 */
abstract class AUpgrade extends \XLite\View\Dialog
{
    /**
     * Upgrade entries
     *
     * @var array
     */
    protected $entries = null;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'upgrade';

        return $result;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return 'upgrade';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        $result = parent::getListName();

        if (!empty($result)) {
            $result .= '.';
        }

        return $result . 'upgrade';
    }

    /**
     * Return list of modules and/or core to upgrade
     *
     * @return array
     */
    protected function getUpgradeEntries()
    {
        if (!isset($this->entries)) {
            $this->entries = \XLite\Upgrade\Cell::getInstance()->getEntries();
        }

        return $this->entries;
    }

    /**
     * Return list of modules and/or core to upgrade that hotfix only updates
     *
     * @return array
     */
    protected function getHotfixUpgradeEntries()
    {
        return \XLite\Upgrade\Cell::getInstance()->getHotfixEntries();
    }

    /**
     * Return list of modules and/or core to upgrade that not hotfix only updates
     *
     * @return array
     */
    protected function getNewFeaturesUpgradeEntries()
    {
        return \XLite\Upgrade\Cell::getInstance()->getNewFeaturesEntries();
    }

    /**
     * Return list of modules and/or core to upgrade
     *
     * @return integer
     */
    protected function getUpgradeEntriesCount()
    {
        return count($this->getUpgradeEntries());
    }

    /**
     * Check if passed entry is a module
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry Object to check
     *
     * @return boolean
     */
    protected function isModule(\XLite\Upgrade\Entry\AEntry $entry)
    {
        return $entry instanceof \XLite\Upgrade\Entry\Module\AModule;
	}

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }

    /**
     * Returns unique value for 'id' attribute of module entry tag
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry Upgrade entry
     *
     * @return string
     */
    protected function getEntryId($entry)
    {
        return $entry->getMarketplaceID();
    }
}
