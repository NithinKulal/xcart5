<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Upgrade top box
 *
 * @ListChild (list="admin.main.page.header_wrapper", weight="1000", zone="admin")
 */
class UpgradeTopBox extends \XLite\View\AView
{
    /**
     * Key for storing tmpVars read mark
     */
    const READ_MARK_KEY = 'toplinksMenuReadHash';
    /**
     * Flags
     *
     * @var array
     */
    protected $updateFlags;

    /**
     * Get JS files 
     * 
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'top_links/version_notes/parts/upgrade.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'top_links/version_notes/parts/upgrade.twig';
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return ['upgrade'];
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Return true if box should be active
     *
     * @return boolean
     */
    protected function isUpgradeBoxVisible()
    {
        return $this->isCoreUpgradeAvailable() || $this->areUpdatesAvailable();
    }

    /**
     * Check if there is a new core version
     *
     * @return boolean
     */
    protected function isCoreUpgradeAvailable()
    {
        $flags = $this->getUpdateFlags();

        return !empty($flags[\XLite\Core\Marketplace::FIELD_IS_UPGRADE_AVAILABLE]);
    }

    /**
     * Check if there are updates (new core revision and/or module revisions)
     *
     * @return boolean
     */
    protected function areUpdatesAvailable()
    {
        $flags = $this->getUpdateFlags();
        return !empty($flags[\XLite\Core\Marketplace::FIELD_ARE_UPDATES_AVAILABLE]);
    }

    /**
     * Return upgrade flags
     *
     * @return array
     */
    protected function getUpdateFlags()
    {
        if (!isset($this->updateFlags)) {
            $this->updateFlags = \XLite\Core\Marketplace::getInstance()->checkForUpdates();
        }

        return is_array($this->updateFlags) ? $this->updateFlags : array();
    }

    /**
     * Get container tag attributes 
     * 
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        $data = array();
        $data[] = 'upgrade-box';

        $state = 'opened';
        $tmpVarHash = \XLite\Core\TmpVars::getInstance()->{static::READ_MARK_KEY};
        $realHash = \XLite\Core\Marketplace::getInstance()->unseenUpdatesHash();
        if ($realHash !== $tmpVarHash) {
            \XLite\Core\TmpVars::getInstance()->{static::READ_MARK_KEY} = null;

        } elseif(!empty($tmpVarHash)) {
            $state = 'post-closed';
        }

        $data[] = $state;

        if (!$this->isUpgradeBoxVisible()) {
            $data[] = 'invisible';
        }

        return array(
            'class' => $data,
        );
    }
}
