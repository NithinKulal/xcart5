<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\AccountStatus
 */
class AccountStatus extends \XLite\View\FormField\Select\Regular
{
    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = false;


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Model\Profile::STATUS_ENABLED  => static::t('Enabled'),
            \XLite\Model\Profile::STATUS_DISABLED => static::t('Disabled'),
        );
    }
}
