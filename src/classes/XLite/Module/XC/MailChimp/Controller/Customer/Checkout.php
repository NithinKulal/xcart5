<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Customer;

use \XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp customer subscriptions
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        $subscriptionInfo = \XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_FIELD_NAME};
        $subscribeToAll = \XLite\Core\Request::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME};

        if (isset($subscriptionInfo)) {
            \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_FIELD_NAME} = $subscriptionInfo;
        }

        if (isset($subscribeToAll)) {
            \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME} = $subscribeToAll;
        }

        parent::callAction();
    }

    /**
     * Do payment: Add mailchimp subscriptions post-processing
     *
     * @return void
     */
    protected function doPayment()
    {
        parent::doPayment();

        $this->doProcessMailchimpSubscription();
    }

    /**
     * Do action 'return': Add mailchimp subscriptions post-processing
     *
     * @return void
     */
    protected function doActionReturn()
    {
        parent::doActionReturn();

        $this->doProcessMailchimpSubscription();
    }

    /**
     * Process mailchimp subscription
     *
     * @return void
     */
    protected function doProcessMailchimpSubscription()
    {
        if (!\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured()) {
            return;
        }

        $subscriptionInfo = \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_FIELD_NAME};
        $subscribeToAll = \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME};

        if ($subscriptionInfo || $subscribeToAll) {

            $profile = $this->getOriginalProfile();

            if ($profile) {

                try {
                    if (!isset($subscriptionInfo) && isset($subscribeToAll) && $subscribeToAll) {
                        Core\MailChimp::processSubscriptionAll($profile);

                    } elseif (isset($subscriptionInfo)) {
                        Core\MailChimp::processSubscriptionInput(
                            $profile,
                            $subscriptionInfo
                        );
                    }

                } catch (Core\MailChimpException $e) {
                    \XLite\Core\TopMessage::addError(Core\MailChimp::getMessageTextFromError($e));
                }
            }

            \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_FIELD_NAME} = null;
            \XLite\Core\Session::getInstance()->{Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME} = null;
        }
    }

    /**
     * Get original cart profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getOriginalProfile()
    {
        return $this->getCart()->getOrigProfile();
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }
}
