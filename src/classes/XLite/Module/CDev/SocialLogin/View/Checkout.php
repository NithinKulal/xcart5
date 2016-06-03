<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View;

/**
 * Abstract widget
 */
abstract class Checkout extends \XLite\View\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Defines the anonymous title box
     *
     * @return string
     */
    protected function getSigninAnonymousTitle()
    {
        $authProviders = \XLite\Module\CDev\SocialLogin\Core\AuthManager::getAuthProviders();

        if (empty($authProviders)) {
            $result = parent::getSigninAnonymousTitle();

        } else {
            $params = array(
                'text_before' => static::t('Register with'),
                'text_after'  => static::t('or go to checkout as a New customer'),
                'buttonStyle' => 'icon',
            );

            $result = $this->getWidget($params, '\XLite\Module\CDev\SocialLogin\View\SocialLoginWidget')->getContent();
        }

        return $result;
    }
}
