<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * EntriesList
 *
 * @ListChild (list="admin.center", weight="100", zone="admin")
 */
class EntriesList extends \XLite\View\Upgrade\Step\Prepare\APrepare
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/widget.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return $this->isUpgrade()
            ? parent::getDir() . '/entries_list_upgrade'
            : parent::getDir() . '/entries_list_update';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return $this->isUpgrade()
            ? parent::getListName() . '.entries_list_upgrade'
            : parent::getListName() . '.entries_list_update';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        if (\XLite\Upgrade\Cell::getInstance()->isUpgrade()) {
            $result = static::t(
                'X modules will be upgraded',
                array('count' => $this->getUpgradeEntriesCount())
            );

        } else {
            $result = 'These components will be updated';
        }

        return $result;
    }

    /**
     * Helper to get CSS class
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry Current entry
     *
     * @return string
     */
    protected function getEntryRowCSSClass(\XLite\Upgrade\Entry\AEntry $entry)
    {
        return $this->isModule($entry) ? 'module-entry' : 'core-entry';
    }
}
