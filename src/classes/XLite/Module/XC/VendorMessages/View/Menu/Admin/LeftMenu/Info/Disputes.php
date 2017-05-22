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
class Disputes extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
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
            'vendorDisputesUpdateTimestamp' => $this->getLastUpdateTimestamp(),
        );
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Module\XC\VendorMessages\Main::isAllowDisputes()
            && !\XLite\Core\Auth::getInstance()->isVendor()
            && $this->getCounter();
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = \XLite\Core\TmpVars::getInstance()->vendorDisputesUpdateTimestamp;

        if (!isset($result)) {
            $result = LC_START_TIME;
            \XLite\Core\TmpVars::getInstance()->vendorDisputesUpdateTimestamp = $result;
        }

        return $result;
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
        $list[] = 'disputes-counter';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getIcon()
    {
        return $this->getSVGImage('modules/XC/VendorMessages/images/disputes.svg');
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
                'messages'         => 'D',
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
        return static::t('Disputes');
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->countDisputes();
    }

    // }}}
}
