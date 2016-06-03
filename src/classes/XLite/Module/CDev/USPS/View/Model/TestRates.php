<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\Model;

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
        return 'XLite\Module\CDev\USPS\View\Form\TestRates';
    }

    /**
     * Get list of available schema fields
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
            static::SCHEMA_FIELD_COD_ENABLED,
        );
    }

    /**
     * Alter the default field set
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $result = parent::getTestRatesSchema();
        $result[static::SCHEMA_FIELD_SRC_COUNTRY][static::SCHEMA_CLASS] = 'XLite\View\FormField\Input\Text';
        $result[static::SCHEMA_FIELD_SRC_COUNTRY][static::SCHEMA_ATTRIBUTES] = array('readonly' => 'readonly');

        return $result;
    }

    /**
     * Alter default model object values
     *
     * @return array
     */
    protected function getDefaultModelObjectValues()
    {
        $result = parent::getDefaultModelObjectValues();
        $result[static::SCHEMA_FIELD_SRC_COUNTRY] = 'United States';

        return $result;
    }
}
