<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade;


class UpgradeAccessKeysNotification extends \XLite\View\AView
{
    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    public function getDir()
    {
        return 'upgrade_access_keys_notification';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return sort reset link
     *
     * @return string
     */
    protected function getSoftResetLink()
    {
        return \Includes\SafeMode::getResetURL(true);
    }

    /**
     * Return hard reset link
     *
     * @return string
     */
    protected function getHardResetLink()
    {
        return \Includes\SafeMode::getResetURL();
    }
}