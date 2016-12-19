<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Test shipping rates widget
 */
abstract class TestRates extends \XLite\View\Model\AModel
{
    /**
     * Schema field names
     */
    const SCHEMA_FIELD_WEIGHT           = 'weight';
    const SCHEMA_FIELD_SUBTOTAL         = 'subtotal';
    const SCHEMA_FIELD_SRC_CITY         = 'source_city';
    const SCHEMA_FIELD_SRC_COUNTRY      = 'source_country';
    const SCHEMA_FIELD_SRC_STATE        = 'source_state';
    const SCHEMA_FIELD_SRC_CUSTOM_STATE = 'source_custom_state';
    const SCHEMA_FIELD_SRC_ZIPCODE      = 'source_postal_code';
    const SCHEMA_FIELD_DST_CITY         = 'destination_city';
    const SCHEMA_FIELD_DST_COUNTRY      = 'destination_country';
    const SCHEMA_FIELD_DST_STATE        = 'destination_state';
    const SCHEMA_FIELD_DST_CUSTOM_STATE = 'destination_custom_state';
    const SCHEMA_FIELD_DST_ZIPCODE      = 'destination_postal_code';
    const SCHEMA_FIELD_DST_TYPE         = 'destination_type';
    const SCHEMA_FIELD_COD_ENABLED      = 'cod_enabled';

    const SCHEMA_FIELD_SEP_PACKAGE      = 'sep_package';
    const SCHEMA_FIELD_SEP_SRC_ADDRESS  = 'sep_source_address';
    const SCHEMA_FIELD_SEP_DST_ADDRESS  = 'sep_destination_address';
    const SCHEMA_FIELD_SEP_SHIP_OPTIONS = 'sep_shipment_options';

    /**
     * Schema fields description
     *
     * @var array
     */
    protected $schemaTestRates = array(
        /**
         * Package
         */
        self::SCHEMA_FIELD_SEP_PACKAGE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Package',
        ),
        self::SCHEMA_FIELD_WEIGHT => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Package weight (X)',
        ),
        self::SCHEMA_FIELD_SUBTOTAL => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\FloatInput',
            self::SCHEMA_LABEL    => 'Package subtotal (X)',
        ),

        /**
         * Source address
         */
        self::SCHEMA_FIELD_SEP_SRC_ADDRESS => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Source address',
        ),
        self::SCHEMA_FIELD_SRC_CITY => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'City',
        ),
        self::SCHEMA_FIELD_SRC_COUNTRY => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Country',
            self::SCHEMA_LABEL    => 'Country',
            self::SCHEMA_ATTRIBUTES => array('all' => true),
        ),
        self::SCHEMA_FIELD_SRC_STATE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\State',
            self::SCHEMA_LABEL    => 'State',
        ),
        self::SCHEMA_FIELD_SRC_CUSTOM_STATE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'State',
        ),
        self::SCHEMA_FIELD_SRC_ZIPCODE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Postal code',
        ),

        /**
         * Destination address
         */
        self::SCHEMA_FIELD_SEP_DST_ADDRESS => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Destination address',
        ),
        self::SCHEMA_FIELD_DST_CITY => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'City',
        ),
        self::SCHEMA_FIELD_DST_COUNTRY => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\Country',
            self::SCHEMA_LABEL    => 'Country',
            self::SCHEMA_ATTRIBUTES => array('all' => true),
        ),
        self::SCHEMA_FIELD_DST_STATE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\State',
            self::SCHEMA_LABEL    => 'State',
        ),
        self::SCHEMA_FIELD_DST_CUSTOM_STATE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'State',
        ),
        self::SCHEMA_FIELD_DST_ZIPCODE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Postal code',
        ),
        self::SCHEMA_FIELD_DST_TYPE => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\AddressType',
            self::SCHEMA_LABEL    => 'Address type',
        ),

        /**
         * Shipment options
         */
        self::SCHEMA_FIELD_SEP_SHIP_OPTIONS => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Separator\Regular',
            self::SCHEMA_LABEL    => 'Shipment options',
        ),
        self::SCHEMA_FIELD_COD_ENABLED => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox',
            self::SCHEMA_LABEL    => 'Cash on delivery',
        ),
    );

    /**
     * Default form values
     *
     * @var array
     */
    protected $defaultValues;

    /**
     * Get list of available schema fields
     *
     * @return array
     */
    protected function getAvailableSchemaFields()
    {
        return array();
    }

    /**
     * Get the associative array of section fields where keys are separators of fields groups
     *
     * @return array
     */
    protected function getSchemaFieldsSubsections()
    {
        return array(
            static::SCHEMA_FIELD_SEP_PACKAGE => array(
                static::SCHEMA_FIELD_WEIGHT,
                static::SCHEMA_FIELD_SUBTOTAL,
            ),
            static::SCHEMA_FIELD_SEP_SRC_ADDRESS => array(
                static::SCHEMA_FIELD_SRC_CITY,
                static::SCHEMA_FIELD_SRC_COUNTRY,
                static::SCHEMA_FIELD_SRC_STATE,
                static::SCHEMA_FIELD_SRC_CUSTOM_STATE,
                static::SCHEMA_FIELD_SRC_ZIPCODE,
            ),
            static::SCHEMA_FIELD_SEP_DST_ADDRESS => array(
                static::SCHEMA_FIELD_DST_CITY,
                static::SCHEMA_FIELD_DST_COUNTRY,
                static::SCHEMA_FIELD_DST_STATE,
                static::SCHEMA_FIELD_DST_CUSTOM_STATE,
                static::SCHEMA_FIELD_DST_ZIPCODE,
                static::SCHEMA_FIELD_DST_TYPE,
            ),
            static::SCHEMA_FIELD_SEP_SHIP_OPTIONS => array(
                static::SCHEMA_FIELD_COD_ENABLED,
            ),
        );
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $result = $this->getFieldsBySchema($this->getTestRatesSchema());

        // For country <-> state synchronization
        $this->setStateSelectorIds($result);

        return $result;
    }

    /**
     * Get fields for schema
     *
     * @return array
     */
    protected function getTestRatesSchema()
    {
        $result = $this->schemaTestRates;

        $fields = $this->getAvailableSchemaFields();

        // Add separators
        $separators = $this->getSchemaFieldsSubsections();
        foreach ($separators as $k => $v) {
            if (array_intersect($v, $fields)) {
                $fields[] = $k;
            }
        }

        // Add custom state fields
        if (in_array(static::SCHEMA_FIELD_SRC_STATE, $fields)) {
            $fields[] = static::SCHEMA_FIELD_SRC_CUSTOM_STATE;
        }

        if (in_array(static::SCHEMA_FIELD_DST_STATE, $fields)) {
            $fields[] = static::SCHEMA_FIELD_DST_CUSTOM_STATE;
        }

        // Get list of schema fields
        if ($fields) {
            foreach ($result as $k => $v) {
                if (!in_array($k, $fields)) {
                    unset($result[$k]);

                } else {
                    if (self::SCHEMA_FIELD_WEIGHT === $k) {
                        $result[$k][self::SCHEMA_LABEL_PARAMS] = array(
                            'units' => \XLite\Core\Translation::translateWeightSymbol(),
                        );

                    } elseif (self::SCHEMA_FIELD_SUBTOTAL === $k) {
                        $result[$k][self::SCHEMA_LABEL_PARAMS] = array(
                            'units' => \XLite::getInstance()->getCurrency()->getCurrencySymbol(),
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Pass the DOM IDs of the "State" selectbox to the "CountrySelector" widget
     *
     * @param array &$fields Widgets list
     *
     * @return void
     */
    protected function setStateSelectorIds(array &$fields)
    {
        if (isset($fields[static::SCHEMA_FIELD_SRC_STATE])
            && isset($fields[static::SCHEMA_FIELD_SRC_CUSTOM_STATE])
            && isset($fields[static::SCHEMA_FIELD_SRC_COUNTRY])
        ) {
            $fields[static::SCHEMA_FIELD_SRC_COUNTRY]->setStateSelectorIds(
                $fields[static::SCHEMA_FIELD_SRC_STATE]->getFieldId(),
                $fields[static::SCHEMA_FIELD_SRC_CUSTOM_STATE]->getFieldId()
            );
        }

        if (isset($fields[static::SCHEMA_FIELD_DST_STATE])
            && isset($fields[static::SCHEMA_FIELD_DST_CUSTOM_STATE])
            && isset($fields[static::SCHEMA_FIELD_DST_COUNTRY])
        ) {
            $fields[static::SCHEMA_FIELD_DST_COUNTRY]->setStateSelectorIds(
                $fields[static::SCHEMA_FIELD_DST_STATE]->getFieldId(),
                $fields[static::SCHEMA_FIELD_DST_CUSTOM_STATE]->getFieldId()
            );
        }
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        if (is_null($this->defaultValues)) {
            $this->defaultValues = $this->getDefaultModelObjectValues();
        }

        return isset($this->defaultValues[$name]) ? $this->defaultValues[$name] : null;
    }

    /**
     * Get default model object values
     *
     * @return array
     */
    protected function getDefaultModelObjectValues()
    {
        $config = \XLite\Core\Config::getInstance();

        return array(
            self::SCHEMA_FIELD_WEIGHT => 0.100,
            self::SCHEMA_FIELD_SUBTOTAL => 100,
            self::SCHEMA_FIELD_SRC_CITY => $config->Company->origin_city,
            self::SCHEMA_FIELD_SRC_COUNTRY => $config->Company->origin_country,
            self::SCHEMA_FIELD_SRC_STATE => $config->Company->origin_state,
            self::SCHEMA_FIELD_SRC_CUSTOM_STATE => $config->Company->origin_custom_state,
            self::SCHEMA_FIELD_SRC_ZIPCODE => $config->Company->origin_zipcode,
            self::SCHEMA_FIELD_DST_CITY => $config->Shipping->anonymous_city,
            self::SCHEMA_FIELD_DST_COUNTRY => $config->Shipping->anonymous_country,
            self::SCHEMA_FIELD_DST_STATE => $config->Shipping->anonymous_state,
            self::SCHEMA_FIELD_DST_CUSTOM_STATE => $config->Shipping->anonymous_custom_state,
            self::SCHEMA_FIELD_DST_ZIPCODE => $config->Shipping->anonymous_zipcode,
            self::SCHEMA_FIELD_DST_TYPE => \XLite\View\FormField\Select\AddressType::TYPE_RESIDENTIAL,
        );
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Get rates',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action always-enabled',
            )
        );

        $result['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            )
        );

        return $result;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
    }
}
