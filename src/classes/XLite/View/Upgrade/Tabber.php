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
        $list = array();

        $modulesInfo = \XLite\Core\Database::getRepo('XLite\Model\Module')->getUpgradeModulesInfoHash();

        if ($this->isUpdateModeSelectorAvailable()) {
            $isHotfixMode = (bool)\XLite\Core\Session::getInstance()->upgradeHotfixMode;
            $url = $this->buildURL('upgrade', 'toggleHotfixMode');
            $list['hotfix'] = array(
                'title'    => static::t('Hotfixes mode'),
                'url'      => !$isHotfixMode ? $url : null,
                'selected' => $isHotfixMode,
            );
            $list['updates'] = array(
                'title'    => static::t('Updates mode'),
                'url'      => $isHotfixMode ? $url : null,
                'selected' => !$isHotfixMode,
            );
        }

        return $list;
    }
}
