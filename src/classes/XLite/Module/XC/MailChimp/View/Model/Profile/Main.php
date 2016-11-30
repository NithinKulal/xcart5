<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Model\Profile;

use XLite\Module\XC\MailChimp\Core;

/**
 * Top menu widget
 * 
 * @Decorator\Before ("XC\TwoFactorAuthentication")
 */
abstract class Main extends \XLite\View\Model\Profile\Main implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/MailChimp/profile/subscriptions_register.css'
            ]
        );
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if (
            'register' == \XLite\Core\Request::getInstance()->mode
            && $this->hasActiveMailChimpLists()
        ) {
            $additionalSchema = [
                Core\MailChimp::SUBSCRIPTION_TO_ALL_FIELD_NAME => [
                    self::SCHEMA_CLASS      => '\XLite\Module\XC\MailChimp\View\FormField\CheckboxWithCaption',
                    self::SCHEMA_LABEL      => '',
                    self::SCHEMA_REQUIRED   => false,
                    self::SCHEMA_IS_CHECKED => true,
                    \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS => 'input input-checkbox mailchimp-subscribe-all',
                ]
            ];
            $schema = array_merge($this->mainSchema, $additionalSchema);

            // Modify the main schema
            $this->mainSchema = $schema;
        }

        return parent::getFormFieldsForSectionMain();
    }

    /**
     * Return MailChimp list
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    protected function getMailChimpSubscriptionsList()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        $lists = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->getAllMailChimpLists();

        $return = array();

        foreach ($lists as $list) {
            if ($list->getEnabled()) {
                $return[] = $list;
            } elseif (
                !is_null($profile)
                && $list->isProfileSubscribed($profile)
            ) {
                $return[] = $list;
            }
        }

        return $return;
    }

    /**
     * Check if there are any active MailChimp lists
     *
     * @return boolean
     */
    protected function hasActiveMailChimpLists()
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->countActiveMailChimpLists() > 0;
    }
}
