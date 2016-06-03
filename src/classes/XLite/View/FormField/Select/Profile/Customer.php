<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Profile;

/**
 * Customer profiles select
 */
class Customer extends \XLite\View\FormField\Select\Profile\AProfile
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Profile')->findAllCustomerAccounts() as $profile) {
            $list[$profile->getProfileId()] = $profile->getLogin();
        }

        return $list;
    }
}
