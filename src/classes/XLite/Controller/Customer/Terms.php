<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Terms and conditions page
 */
class Terms extends \XLite\Controller\Customer\Category
{
    /**
     * Controller parameters list
     *
     * @var array
     */
    protected $params = array('target');

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return true;
    }

    /**
     * Return title of page
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Terms and conditions');
    }
}
