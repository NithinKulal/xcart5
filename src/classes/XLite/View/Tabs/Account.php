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
 * @ListChild (list="center")
 */
class Account extends \XLite\View\Tabs\ATabs
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
        $list[] = 'order_list';
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

            $this->profile = null === $profileId
                ? \XLite\Core\Auth::getInstance()->getProfile()
                : \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

        }

        return $this->profile;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'order_list' => [
                'weight'   => 100,
                'title'    => static::t('Orders'),
                'template' => 'account/order_list.twig',
            ],
            'address_book' => [
                'weight'   => 200,
                'title'    => static::t('Address book'),
                'template' => 'account/address_book.twig',
            ],
            'profile' => [
                'weight'   => 300,
                'title'    => static::t('Details'),
                'template' => 'account/account.twig',
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
