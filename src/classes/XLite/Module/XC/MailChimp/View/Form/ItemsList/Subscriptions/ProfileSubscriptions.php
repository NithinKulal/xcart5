<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Form\ItemsList\Subscriptions;

/**
 * MailChimp mail lists
 */
class ProfileSubscriptions extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'mailchimp_subscriptions';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Return list of additional params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();

        $params += array(
            'profile_id' => \XLite\Core\Request::getInstance()->profile_id,
        );

        return $params;
    }
}
