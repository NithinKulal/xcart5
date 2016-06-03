<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Address type selector
 */
class AddressType extends \XLite\View\FormField\Select\Regular
{
    const TYPE_RESIDENTIAL = 'R';
    const TYPE_COMMERCIAL  = 'C';

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::TYPE_RESIDENTIAL => static::t('Residential'),
            static::TYPE_COMMERCIAL  => static::t('Commercial'),
        );
    }
}
