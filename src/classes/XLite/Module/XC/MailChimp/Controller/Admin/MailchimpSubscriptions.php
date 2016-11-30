<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp customer subscriptions
 */
class MailchimpSubscriptions extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Module\XC\MailChimp\Main::isMailChimpConfigured();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'MailChimp news lists';
    }

    /**
     * Do action "update"
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        try {
            Core\MailChimp::processSubscriptionInput(
                $this->getProfile(),
                \XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_FIELD_NAME}
            );
        } catch (Core\MailChimpException $e) {
            \XLite\Core\TopMessage::addError(Core\MailChimp::getMessageTextFromError($e));
        }
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(
            \XLite\Core\Request::getInstance()->profile_id
        );
    }
}
