<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NewsletterSubscriptions\Controller\Customer;

/**
 * NewsletterSubscriptions controller
 */
class NewsletterSubscriptions extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        $email = \XLite\Core\Request::getInstance()->newlettersubscription_email;

        if (!$this->isSubscribedAlready($email)) {
            $this->doSubscribe($email);
        }

        $this->setPureAction();
    }

    /**
     * Check if passed email already in subscription
     *
     * @param  string  $email Email
     *
     * @return boolean
     */
    protected function isSubscribedAlready($email)
    {
        return (bool) \XLite\Core\Database::getRepo('XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber')
            ->findOneByEmail($email);
    }

    /**
     * Create subscriber
     *
     * @param  string  $email Email
     */
    protected function doSubscribe($email)
    {
        $subscriber = new \XLite\Module\XC\NewsletterSubscriptions\Model\Subscriber();
        $subscriber->setEmail($email);

        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $subscriber->setProfile(
                \XLite\Core\Auth::getInstance()->getProfile()
            );
        }

        \XLite\Core\Database::getEM()->persist($subscriber);
        \XLite\Core\Database::getEM()->flush($subscriber);
    }
}
