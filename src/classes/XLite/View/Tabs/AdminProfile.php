<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to user profile section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AdminProfile extends \XLite\View\Tabs\ATabs
{
    /**
     * User profile object
     *
     * @var \XLite\Model\Profile
     */
    protected $profile;

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'profile';
        $list[] = 'address_book';

        return $list;
    }

    /**
     * getProfile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        if (null === $this->profile) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;
            if (null === $profileId) {
                $this->profile = \XLite\Core\Auth::getInstance()->getProfile();

            } else {
                $this->profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);
            }
        }

        return $this->profile;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'profile' => [
                'weight'   => 100,
                'title'    => static::t('Account details'),
                'template' => 'profile/account.twig',
            ],
            'address_book' => [
                'weight'   => 200,
                'title'    => static::t('Address book'),
                'template' => 'profile/address_book.twig',
            ],
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        if (\XLite\Controller\Customer\Profile::getInstance()->isRegisterMode()) {
            $this->tabs = ['profile' => $this->tabs['profile']];
        }

        return parent::prepareTabs();
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
}
