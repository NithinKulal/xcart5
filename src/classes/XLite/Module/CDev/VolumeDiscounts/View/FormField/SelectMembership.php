<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Membership selector widget
 */
class SelectMembership extends \XLite\View\FormField\Select\Membership
{
    /**
     * shortName
     *
     * @var   string
     */
    protected $shortName = 'membership';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '' => static::t('All customers'),
        ) + $this->getMembershipsList();
    }
}
