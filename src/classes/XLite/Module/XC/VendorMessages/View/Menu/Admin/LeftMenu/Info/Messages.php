<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Menu\Admin\LeftMenu\Info;

/**
 * Messages count
 */
class Messages extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/VendorMessages/info.css';

        return $list;
    }


    /**
     * Check if data is updated (must be fast)
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    public function getCacheParameters()
    {
        return array(
            'vendorMessagesUpdateTimestamp' => $this->getLastUpdateTimestamp(),
        );
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCounter();
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $pid = $this->getTargetProfileId();
        $result = \XLite\Core\TmpVars::getInstance()->vendorMessagesUpdateTimestamp;

        if (!isset($result) || !is_array($result)) {
            $result = array($pid => LC_START_TIME);
            \XLite\Core\TmpVars::getInstance()->vendorMessagesUpdateTimestamp = $result;

        } elseif (!isset($result[$pid])) {
            $result[$pid] = LC_START_TIME;
            \XLite\Core\TmpVars::getInstance()->vendorMessagesUpdateTimestamp = $result;
        }

        return $result[$pid];
    }

    /**
     * Get notification target user profile id
     *
     * @return integer
     */
    protected function getTargetProfileId()
    {
        return 0;
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
        $list[] = 'messages-counter';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getIcon()
    {
        return $this->getSVGImage('modules/XC/VendorMessages/images/mail.svg');
    }

    /**
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL(
            'messages',
            'search',
            array(
                'messages'         => 'U',
                'messageSubstring' => '',
            )
        );
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Messages');
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countUnread();
    }

    // }}}
}
