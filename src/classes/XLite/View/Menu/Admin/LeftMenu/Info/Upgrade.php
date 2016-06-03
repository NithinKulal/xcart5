<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu\Info;

/**
 * Node lazy load abstract class
 */
class Upgrade extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
{
    const STATUS_NO_UPDATES = '';
    const STATUS_UPDATES_PRESENT = 'updates';
    const STATUS_UPGRADE_PRESENT = 'upgrade';

    /**
     * Runtime cache
     *
     * @var array
     */
    protected $status = null;

    /**
     * Check if content is updated
     *
     * @return boolean
     */
    public function isUpdated()
    {
        $status = $this->getStatus();

        return $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp()
            && $status['status'] !== static::STATUS_NO_UPDATES;
    }

    /**
     * Returns count of unread messages
     *
     * @return integer
     */
    public function getUnreadCount()
    {
        $status = $this->getStatus();

        return (empty($status) || $status['status'] === static::STATUS_NO_UPDATES)
            ? 0
            : parent::getUnreadCount();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    public function getCacheParameters()
    {
        return array(
            'upgradeInfoStatusTimestamp' => $this->getLastUpdateTimestamp(),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $status = $this->getStatus();

        return parent::isVisible() && $status && $status['status'] !== static::STATUS_NO_UPDATES;
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = \XLite\Core\TmpVars::getInstance()->upgradeInfoStatusTimestamp;
        $status = $this->calcUpgradeInfoStatus();

        if (!isset($result) || $this->isStatusChanged($status)) {
            $result = LC_START_TIME;
            $this->setLastUpdateTimestamp($result);
            \XLite\Core\TmpVars::getInstance()->upgradeInfoStatus = $status;
        }

        return $result;
    }

    /**
     * Set update timestamp
     *
     * @param integer $timestamp Timestamp
     *
     * @return void
     */
    protected function setLastUpdateTimestamp($timestamp)
    {
        \XLite\Core\TmpVars::getInstance()->upgradeInfoStatusTimestamp = $timestamp;
    }

    /**
     * Return upgrade status
     *
     * @return array
     */
    protected function calcUpgradeInfoStatus()
    {
        $status = $this->isCoreUpgradeAvailable()
            ? static::STATUS_UPGRADE_PRESENT
            : ($this->areUpdatesAvailable() ? static::STATUS_UPDATES_PRESENT : static::STATUS_NO_UPDATES);

        return array(
            'status' => $status,
            'count' => $this->getCounter()
        );
    }

    /**
     * Returns calculated status
     *
     * @return array
     */
    protected function getStatus()
    {
        if (!isset($this->status)) {
            $this->status = $this->calcUpgradeInfoStatus();
        }

        return $this->status;
    }


    /**
     * Check status changed
     *
     * @param array $status Current status
     *
     * @return boolean
     */
    protected function isStatusChanged($status)
    {
        $result = false;
        $savedStatus = \XLite\Core\TmpVars::getInstance()->upgradeInfoStatus;

        if (!is_array($savedStatus) || !isset($savedStatus['status'])) {
            $result = true;
        }

        if ($savedStatus['status'] !== $status['status']
            || $savedStatus['count'] !== $status['count']
        ) {
            $result = true;
        }

        return $result;
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

    // {{{ View helpers

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = parent::getNodeStyleClasses();
        $list[] = 'upgrade';

        return $list;
    }


    /**
     * Returns icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('images/upgrade.svg');
    }

    /**
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        return ($this->isCoreUpgradeAvailable() && !$this->areUpdatesAvailable())
            ? $this->buildURL('upgrade', 'start_upgrade')
            : $this->buildURL('upgrade', '', array('mode' => 'install_updates'));
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return ($this->isCoreUpgradeAvailable() && !$this->areUpdatesAvailable())
            ? static::t('Upgrade available')
            : static::t('Updates are available');
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();

        return count($entries);
    }

    // }}}
}
