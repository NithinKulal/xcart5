<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Tabs;

/**
 * MailChimp subscriptions tab
 */
abstract class Account extends \XLite\View\Tabs\Account implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return void
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_subscriptions';

        return $return;
    }

    /**
     * Check if MailChimp module is configured and have lists
     *
     * @return boolean
     */
    protected function isMailChimpConfigured()
    {
        return \XLite\Module\XC\MailChimp\Main::isMailChimpConfigured();
    }

    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if ($this->isMailChimpConfigured()) {
            $tabs['mailchimp_subscriptions'] = array(
                'title'    => static::t('News list subscriptions'),
                'template' => 'modules/XC/MailChimp/profile/subscriptions_tab.twig',
            );
        }

        return $tabs;
    }
}
