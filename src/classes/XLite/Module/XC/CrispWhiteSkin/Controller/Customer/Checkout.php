<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

/**
 * Checkout
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function getSigninTitle()
    {
        return $this->isRegisterMode()
            ? static::t('Create new account')
            : static::t('Sign in');
    }

    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return 'register' === \XLite\Core\Request::getInstance()->mode && !$this->isCheckoutAvailable();
    }
}
