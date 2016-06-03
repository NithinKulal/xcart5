<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Address book 
 */
class AddressBook extends \XLite\View\AView
{
    /**
     * Widget arguments names
     */
    const PARAM_PROFILE = 'profile';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'account/address_book/body.twig';
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
            static::PARAM_PROFILE => new \XLite\Model\WidgetParam\TypeObject('Profile', null, false, '\XLite\Model\Profile'),
        );
    }

    /**
     * Get profile 
     * 
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getParam(static::PARAM_PROFILE);
    }

    /**
     * Get addresses 
     * 
     * @return array
     */
    protected function getAddresses()
    {
        $list = $this->getProfile()->getAddresses()->toArray();
        foreach ($list as $i => $address) {
            if ($address->getIsWork()) {
                unset($list[$i]);
            }
        }

        return array_values($list);
    }
}

