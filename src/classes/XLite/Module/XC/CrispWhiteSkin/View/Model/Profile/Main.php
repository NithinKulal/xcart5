<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\Main
 *
 * @Decorator\Depend("CDev\SocialLogin")
 */
class Main extends \XLite\View\Model\Profile\Main implements \XLite\Base\IDecorator
{
    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        unset($result['social-login']);

        if ($this->getTarget() == 'checkout') {
            $result['signin'] = new \XLite\View\Button\SimpleLink(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL => static::t('Sign in'),
                    \XLite\View\Button\AButton::PARAM_STYLE => '',
                    \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('checkout')
                )
            );
        } else {
            $result['signin'] = new \XLite\View\Button\PopupLoginLink(
                array()
            );
        }

        return $result;
    }
}
