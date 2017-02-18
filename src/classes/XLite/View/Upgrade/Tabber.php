<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;

/**
 * Upgrades page tabber
 *
 * @ListChild (list="admin.center", zone="admin", weight="1000")
 */
class Tabber extends \XLite\View\Tabber
{
    /**
     * Return list of allowed targets
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'upgrade';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->isUpdate()
            && \XLite\Upgrade\Cell::getInstance()->getEntries();
    }

    /**
     * Get prepared pages array for tabber
     *
     * @return array
     */
    protected function getTabberPages()
    {
        if (null === $this->pages) {
            $this->pages = [];

            $dialogPages = $this->defineUpgradePageTabs();

            foreach ($dialogPages as $page => $data) {
                $linkTemplate = null;
                $p = new \XLite\Base();
                $p->set('title', $data['title']);
                $p->set('url', !empty($data['url']) ? $data['url'] : null);
                $p->set('linkTemplate', !empty($data['url']) ? null : 'upgrade/install_updates/tab_active.twig');
                $p->set('key', $page);
                $p->set('selected', $data['selected']);
                $this->pages[] = $p;
            }
        }

        return $this->pages;
    }

    /**
     * Get upgrade page tabs definition
     *
     * @return array
     */
    protected function defineUpgradePageTabs()
    {
        $list = [];

        if ($this->isUpdate()) {
            $upgradeCell = \XLite\Upgrade\Cell::getInstance();
            $isHotfixMode = (bool)\XLite\Core\Session::getInstance()->upgradeHotfixMode;
            $url = $this->buildURL('upgrade', 'toggleHotfixMode');

            if ($upgradeCell->hasHotfixEntries() || $this->isUpdateModeSelectorAvailable()) {
                $list['hotfix'] = [
                    'title'    => static::t('Minor update'),
                    'url'      => !$isHotfixMode ? $url : null,
                    'selected' => $isHotfixMode,
                ];
            }

            if ($upgradeCell->hasNewFeaturesEntries() || $this->isUpdateModeSelectorAvailable()) {
                $list['updates'] = [
                    'title'    => static::t('Major upgrade'),
                    'url'      => $isHotfixMode ? $url : null,
                    'selected' => !$isHotfixMode,
                ];
            }

            if (count($list) === 1) {
                $list[array_keys($list)[0]]['selected'] = true;
            }
        }

        return $list;
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return 0 < count($this->getTabberPages());
    }
}
