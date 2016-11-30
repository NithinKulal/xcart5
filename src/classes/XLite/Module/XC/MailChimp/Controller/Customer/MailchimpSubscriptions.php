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
class MailchimpSubscriptions extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getMainTitle()
    {
        return static::t('News list subscriptions');
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('News list subscriptions');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
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
        return \XLite\Core\Auth::getInstance()->getProfile();
    }
}
