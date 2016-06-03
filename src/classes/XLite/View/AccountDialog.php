<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Account page dialog
 */
class AccountDialog extends \XLite\View\AView
{
    /**
     * User profile object
     *
     * @var \XLite\Model\Profile
     */
    protected $profile;

    /**
     * getProfile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        if (null === $this->profile) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;

            $this->profile = null === $profileId
                ? \XLite\Core\Auth::getInstance()->getProfile()
                : \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

        }

        return $this->profile;
    }

    protected function getDefaultTemplate()
    {
        return 'account/account.twig';
    }

    /**
     * Returns an URL to a tab
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;

        return $this->buildURL($target, '', null === $profileId ? [] : ['profile_id' => $profileId]);
    }

    /**
     * getTitle
     * @todo: move to controller?
     *
     * @return string
     */
    protected function getTitle()
    {
        return \XLite\Controller\Customer\Profile::getInstance()->isRegisterMode()
            ? static::t('New account')
            : static::t('My account');
    }
}
