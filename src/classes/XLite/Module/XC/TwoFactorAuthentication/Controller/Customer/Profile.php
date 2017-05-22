<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Customer;

/**
 * Profile management controller
 */
class Profile extends \XLite\Controller\Customer\Profile implements \XLite\Base\IDecorator
{
    /**
     * Modify profile action
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if (\XLite\Core\Request::getInstance()->mode != 'register')
        {
            $oldNumber = $this->getProfile()->getAuthPhoneNumber() . $this->getProfile()->getAuthPhoneCode();
            $newNumber = \XLite\Core\Request::getInstance()->auth_phone_number
                . \XLite\Core\Request::getInstance()->auth_phone_code;

            if ($oldNumber != $newNumber) {
                \XLite\Core\Database::getRepo('XLite\Model\Profile')
                    ->clearAuthyIdById($this->getProfile()->getProfileId());
            }
        }

        parent::doActionUpdate();
    }
}