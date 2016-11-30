<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\FormField\Select;

/**
 * Select "Yes / No"
 */
class MailChimpSubscription extends \XLite\View\FormField\Select\Regular
{
    const PARAM_PROFILE = 'profile';

    const NO_SUBSCRIPTION = '';

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
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isOptionSelected($value)
    {
        $return = false;
        $currentSubscriptions = array();

        $profile = $this->getProfile();

        if (!is_null($profile)) {
            $currentSubscriptions = $this->getProfile()->getMailChimpListsIds();

            $return = in_array($value, $currentSubscriptions);
        }

        if (is_null($profile)) {
            $defaultListId = \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
                ->getDefaultListId();

            if ($defaultListId == $value) {
                $return = true;
            }
        }

        return $return;
    }


    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $return = array(
            self::NO_SUBSCRIPTION    => static::t('No subscription')
        );

        foreach ($this->getAllActiveMailChimpLists() as $list) {
            $return[$list->getId()] = $list->getName();
        }

        return $return;
    }

    /**
     * Get all active MailChimp lists
     *
     * @return \XLite\Module\XC\MailChimp\Model\MailChimpList[]
     */
    protected function getAllActiveMailChimpLists()
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\XC\MailChimp\Model\MailChimpList')
            ->getActiveMailChimpLists();
    }

    /**
     * Get current profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getWidgetParams(self::PARAM_PROFILE)->value;
    }
}
