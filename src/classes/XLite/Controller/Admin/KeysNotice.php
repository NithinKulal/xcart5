<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * License keys notice page controller
 */
class KeysNotice extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return true if unallowed modules should be ignored on current page
     *
     * @return boolean
     */
    protected function isIgnoreUnallowedModules()
    {
        return true;
    }

    /**
     * Do action 'Re-check'
     *
     * @return void
     */
    protected function doActionRecheck()
    {
        // Clear cahche of some marketplace actions
        \XLite\Core\Marketplace::getInstance()->clearActionCache(
            array(
                \XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES,
                \XLite\Core\Marketplace::ACTION_CHECK_ADDON_KEY,
                \XLite\Core\Marketplace::INACTIVE_KEYS,
            )
        );

        \XLite\Core\Marketplace::getInstance()->getAddonsList(0);

        $returnUrl = \XLite\Core\Request::getInstance()->returnUrl ?: $this->buildURL('main');

        $this->setReturnURL($returnUrl);
    }
}
