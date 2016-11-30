<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Model;

use XLite\Core\Request;
use \XLite\Module\XC\MailChimp\Core;
use XLite\Module\XC\MailChimp\Core\MailChimpQueue;
use XLite\Module\XC\MailChimp\Main;

/**
 * Class represents an order
 */
abstract class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    public function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        if ($this->isECommerce360Cart()
            && Core\MailChimp::hasAPIKey()
            && Main::isMailChimpAbandonedCartEnabled()
            && !$this->getOrderNumber()
        ) {
            MailChimpQueue::getInstance()->addAction(
                'cartUpdate',
                new Core\Action\CartUpdate($this)
            );
        }
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        if ($this->isECommerce360Order()
            && Core\MailChimp::hasAPIKey()
            && Main::isMailChimpECommerceConfigured()
        ) {
            try {
                $result = Core\MailChimp::getInstance()->createOrder($this);
                if ($result) {
                    Core\MailChimp::getInstance()->removeCart($this);
                }
            } catch (\Exception $e) {
                \XLite\Logger::getInstance()->log($e->getMessage());
            }
        }

        $profile = $this->getAvailableProfile();

        if (
            isset($profile)
            && $profile->hasMailChimpSubscriptions()
        ) {
            $profile->checkSegmentsConditions();
        }
    }

    /**
     * Check if the order needs to send ECommerce360 data
     *
     * @return boolean
     */
    protected function isECommerce360Order()
    {
        return isset(Request::getInstance()->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
            && !empty(Request::getInstance()->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
            && isset(Request::getInstance()->{Core\Request::MAILCHIMP_USER_ID})
            && !empty(Request::getInstance()->{Core\Request::MAILCHIMP_USER_ID});
    }

    /**
     * Check if the order needs to send ECommerce360 data
     *
     * @return boolean
     */
    protected function isECommerce360Cart()
    {
        return isset(Request::getInstance()->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
        && !empty(Request::getInstance()->{Core\Request::MAILCHIMP_CAMPAIGN_ID})
        && isset(Request::getInstance()->{Core\Request::MAILCHIMP_USER_ID})
        && !empty(Request::getInstance()->{Core\Request::MAILCHIMP_USER_ID});
    }

    /**
     * Get available profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getAvailableProfile()
    {
        return $this->getOrigProfile() ? $this->getOrigProfile() : $this->getProfile();
    }
}
