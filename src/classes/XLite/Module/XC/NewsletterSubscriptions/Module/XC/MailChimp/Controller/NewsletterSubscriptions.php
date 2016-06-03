<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Module\XC\MailChimp\Controller;

use XLite\Module\XC\MailChimp\Core;

/**
 * NewsletterSubscriptions controller
 *
 * @Decorator\Depend ("XC\MailChimp")
 */
class NewsletterSubscriptions extends \XLite\Module\XC\NewsletterSubscriptions\Controller\Customer\NewsletterSubscriptions implements \XLite\Base\IDecorator
{
    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        if ($this->isMailChimpConfigured()) {
            $this->doSubscribeToMailChimp();
        } else {
            parent::doActionSubscribe();
        }
    }

    /**
     * Check if MailChimp module is configured and have lists
     *
     * @return boolean
     */
    protected function isMailChimpConfigured()
    {
        $listsRepo = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList');

        return Core\MailChimp::hasAPIKey()
            && 0 < $listsRepo->countActiveMailChimpLists();
    }

    /**
     * Subscribe to mailchimp
     */
    protected function doSubscribeToMailChimp()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if (!$profile) {
            $profile = new \XLite\Model\Profile;
            $profile->setLogin(\XLite\Core\Request::getInstance()->email);
            $profile->setAnonymous(true);
            $profile->create();
        }

        \XLite\Module\XC\MailChimp\Core\MailChimp::processSubscriptionAll(
            $profile
        );
    }
}
