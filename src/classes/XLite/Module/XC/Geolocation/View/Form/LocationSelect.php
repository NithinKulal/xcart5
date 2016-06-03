<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\Form;

/**
 * Location selector
 */
class LocationSelect extends \XLite\View\Form\AForm
{
    /**
     * Get default form target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'location_select';
    }

    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'change_location';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        return parent::getCommonFormParams() + array(
            'widget' => '\XLite\Module\XC\Geolocation\View\LocationSelect',
        );
    }
}
