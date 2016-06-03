<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\Model;

/**
 * TestRates widget
 */
class TestRates extends \XLite\View\Model\TestRates
{
    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\CDev\FedEx\View\Form\TestRates';
    }

    /**
     * Returns the list of related targets
     *
     * @return array
     */
    protected function getAvailableSchemaFields()
    {
        return array(
            static::SCHEMA_FIELD_WEIGHT,
            static::SCHEMA_FIELD_SUBTOTAL,
            static::SCHEMA_FIELD_SRC_COUNTRY,
            static::SCHEMA_FIELD_SRC_ZIPCODE,
            static::SCHEMA_FIELD_DST_COUNTRY,
            static::SCHEMA_FIELD_DST_ZIPCODE,
            static::SCHEMA_FIELD_DST_TYPE,
            static::SCHEMA_FIELD_COD_ENABLED,
        );
    }
}
