<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\ItemsList\Subscriptions;

use XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp mail lists
 */
abstract class AMailChimpSubscriptions extends \XLite\View\Container
{
    const PARAM_PROFILE = 'profile';

    /**
     * Get directory
     *
     * @return string
     */
    public function getDir()
    {
        return 'modules/XC/MailChimp/profile';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PROFILE => new \XLite\Model\WidgetParam\TypeObject(
                'Profile',
                null,
                false,
                '\XLite\Model\Profile'
            )
        );
    }

    /**
     * Get checkbox name from list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getCheckboxName(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return Core\MailChimp::SUBSCRIPTION_FIELD_NAME . '[' . $list->getId() . ']';
    }

    /**
     * Get checkbox ID from list
     *
     * @param \XLite\Module\XC\MailChimp\Model\MailChimpList $list MailChimp list
     *
     * @return string
     */
    protected function getCheckboxId(\XLite\Module\XC\MailChimp\Model\MailChimpList $list)
    {
        return Core\MailChimp::SUBSCRIPTION_FIELD_NAME . '-' . $list->getId();
    }

    /**
     * Get select box ID from list
     *
     * @return string
     */
    protected function getSelectBoxId()
    {
        return Core\MailChimp::SUBSCRIPTION_FIELD_NAME;
    }

    /**
     * Get select box name from list
     *
     * @return string
     */
    protected function getSelectBoxName()
    {
        return Core\MailChimp::SUBSCRIPTION_FIELD_NAME;
    }

    /**
     * Check if display type is select box
     *
     * @return boolean
     */
    protected function isSelectBoxElement()
    {
        return Core\MailChimp::isSelectBoxElement();
    }

    /**
     * Return MailChimp list
     *
     * @param boolean $countOnly Return only number of lists OPTIONAL
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[] | integer
     */
    protected function getData($countOnly = false)
    {
        $return = array();

        $profile = $this->getProfile();

        $lists = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList');

        if ($countOnly) {
            $return = $lists->countActiveMailChimpLists();
        } else {
            $lists = $lists->getAllMailChimpLists();

            foreach ($lists as $list) {
                if ($list->getEnabled()) {
                    $return[] = $list;
                } elseif ($list->isProfileSubscribed($profile)) {
                    $return[] = $list;
                }
            }
        }

        return $return;
    }

    /**
     * Get current profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getParam(self::PARAM_PROFILE);
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getGroupsForSelectBox()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->enabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'enabled',
            true
        );
        $cnd->listEnabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
            'list.enabled',
            true
        );

        return \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpGroup')
            ->search($cnd);
    }
}
