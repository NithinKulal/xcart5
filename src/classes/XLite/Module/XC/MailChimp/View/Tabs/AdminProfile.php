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
abstract class AdminProfile extends \XLite\View\Tabs\AdminProfile implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'mailchimp_subscriptions';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineTabs()
    {
        $result = parent::defineTabs();

        if (\XLite\Module\XC\MailChimp\Main::isMailChimpConfigured()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $result = $result
                + array(
                    'mailchimp_subscriptions' => array(
                        'weight'   => 500,
                        'title'    => 'MailChimp news lists',
                        'template' => 'modules/XC/MailChimp/profile/subscriptions_tab.twig',
                    )
                );
        }

        return $result;
    }
}
