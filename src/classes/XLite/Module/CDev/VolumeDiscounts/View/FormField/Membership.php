<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Membership form field
 */
class Membership extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Preprocess value before save
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function preprocessValueBeforeSave($value)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Membership')->find(parent::preprocessValueBeforeSave($value));
    }


    /**
     * Get membership name 
     * 
     * @return string
     */
    protected function getMembershipName()
    {
        return $this->getMembership()
            ? $this->getMembership()->getName()
            : null;
    }

    /**
     * Define field class 
     * 
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\CDev\VolumeDiscounts\View\FormField\SelectMembership';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/form_field/membership_view.twig';
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        $value = parent::getEntityValue();

        return $value ? $value->getMembershipId() : null;
    }

    /**
     * Get membership 
     * 
     * @return \XLite\Model\Membership
     */
    protected function getMembership()
    {
        return $this->getEntity()->getMembership();
    }
}

