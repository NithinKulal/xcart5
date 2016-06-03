<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Safe mode
 */
class SafeMode extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Safe mode');
    }

    /**
     * Re-generate safe mode access key
     *
     * @return void
     */
    public function doActionSafeModeKeyRegen()
    {
        \Includes\SafeMode::regenerateAccessKey(true);
        \XLite\Core\TopMessage::addInfo('Safe mode access key has been re-generated');

        $this->setReturnURL($this->buildURL($this->get('target')));
    }

    /**
     * Email safe mode links to the site administrator
     *
     * @return void
     */
    public function doActionEmailLinks()
    {
        \Includes\SafeMode::sendNotification();
        \XLite\Core\TopMessage::addInfo('Safe mode links were emailed');

        $this->setPureAction(true);
    }
}
