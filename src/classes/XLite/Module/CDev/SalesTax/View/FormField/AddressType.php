<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View\FormField;

/**
 * Address type selector
 */
class AddressType extends \XLite\View\FormField\Select\Regular
{
    const ADDRESS_TYPE_BILLING  = 'billing';
    const ADDRESS_TYPE_SHIPPING = 'shipping';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::ADDRESS_TYPE_BILLING  => static::t('Billing address'),
            static::ADDRESS_TYPE_SHIPPING => static::t('Shipping address'),
        );
    }
}
