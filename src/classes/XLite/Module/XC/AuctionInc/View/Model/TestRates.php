<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\Model;

/**
 * TestRates widget
 */
class TestRates extends \XLite\View\Model\TestRates
{
    /**
     * Schema field names
     */
    const SCHEMA_FIELD_DIMENSIONS = 'dimensions';

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\AuctionInc\View\Form\TestRates';
    }

    /**
     * Returns the list of related targets
     *
     * @return array
     */
    protected function getAvailableSchemaFields()
    {
        $result = array(
            static::SCHEMA_FIELD_WEIGHT,
            static::SCHEMA_FIELD_SUBTOTAL,
            static::SCHEMA_FIELD_DIMENSIONS,
            static::SCHEMA_FIELD_DST_COUNTRY,
            static::SCHEMA_FIELD_DST_STATE,
            static::SCHEMA_FIELD_DST_ZIPCODE,
            static::SCHEMA_FIELD_DST_TYPE,
        );

        if (!\XLite\Module\XC\AuctionInc\Main::isSSAvailable()) {
            $result = array_merge(
                $result,
                array(
                    static::SCHEMA_FIELD_SRC_COUNTRY,
                    static::SCHEMA_FIELD_SRC_STATE,
                    static::SCHEMA_FIELD_SRC_ZIPCODE,
                )
            );
        }

        return $result;
    }

    /**
     * Get the associative array of section fields where keys are separators of fields groups
     *
     * @return array
     */
    protected function getSchemaFieldsSubsections()
    {
        $result = parent::getSchemaFieldsSubsections();

        if (!isset($result[static::SCHEMA_FIELD_SEP_PACKAGE])) {
            $result[static::SCHEMA_FIELD_SEP_PACKAGE] = array();
        }

        $result[static::SCHEMA_FIELD_SEP_PACKAGE] = array_merge(
            $result[static::SCHEMA_FIELD_SEP_PACKAGE],
            array(
                static::SCHEMA_FIELD_DIMENSIONS,
            )
        );

        return $result;
    }

    /**
     * Get fields for schema
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $fields = array();
        foreach ($this->schemaTestRates as $fieldName => $field) {
            $fields[$fieldName] = $field;
            if ($fieldName == static::SCHEMA_FIELD_WEIGHT) {
                $fields[static::SCHEMA_FIELD_DIMENSIONS] = array(
                    self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\Dimensions',
                    self::SCHEMA_LABEL    => 'Dimensions',
                );
            }
        }
        $this->schemaTestRates = $fields;

        $this->schemaTestRates[static::SCHEMA_FIELD_SEP_SRC_ADDRESS][static::SCHEMA_LABEL] = 'Origin address';

        return parent::getTestRatesSchema();
    }
}
