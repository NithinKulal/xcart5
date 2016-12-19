<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * \XLite\View\Address
 */
class Address extends \XLite\View\Dialog
{
    /**
     * Widget parameter names
     */
    const PARAM_DISPLAY_MODE    = 'displayMode';
    const PARAM_ADDRESS         = 'address';
    const PARAM_DISPLAY_WRAPPER = 'displayWrapper';

    /**
     * Allowed display modes
     */
    const DISPLAY_MODE_TEXT = 'text';
    const DISPLAY_MODE_FORM = 'form';

    /**
     * Service constants for schema definition
     */
    const SCHEMA_CLASS    = 'class';
    const SCHEMA_LABEL    = 'label';
    const SCHEMA_REQUIRED = 'required';

    /**
     * Schema
     *
     * @var array
     */
    protected $schema = array();

    // {{{ Schema fields

    /**
     * Returns address param
     * 
     * @return void
     */
    protected function getAddress()
    {
        return $this->getParam(static::PARAM_ADDRESS);
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFields()
    {
        $result = $this->schema;

        $fields = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
            ->search(new \XLite\Core\CommonCell(array('enabled' => true)));

        foreach ($fields as $field) {
            $result[$field->getServiceName()] = array(
                static::SCHEMA_CLASS    => $field->getSchemaClass(),
                static::SCHEMA_LABEL    => $field->getName(),
                static::SCHEMA_REQUIRED => $field->getRequired(),
            );
        }

        return $this->getFilteredSchemaFields($result);
    }

    /**
     * Filter schema fields
     *
     * @param array $fields Schema fields to filter
     *
     * @return array
     */
    protected function getFilteredSchemaFields($fields)
    {
        if (!isset($fields['country_code'])) {
            // Country code field is disabled
            // We need leave oonly one state field: selector or text field

            $deleteStateSelector = true;

            $address = $this->getParam(self::PARAM_ADDRESS);

            if ($address && $address->getCountry() && $address->getCountry()->hasStates()) {
                $deleteStateSelector = false;
            }

            if ($deleteStateSelector) {
                unset($fields['state_id']);

            } else {
                unset($fields['custom_state']);
            }
        }

        return $fields;
    }

    // }}}

    /**
     * Get field style
     *
     * @param string $fieldName Field name
     *
     * @return string
     */
    protected function getFieldStyle($fieldName)
    {
        $result = 'address-text-cell address-text-' . $fieldName;

        if (
            \XLite\Core\Database::getRepo('XLite\Model\AddressField')
                ->findOneBy(array('serviceName' => $fieldName, 'additional' => true))
        ) {
            $result .= ' additional-field';
        }

        return $result;
    }

    /**
     * Get field value
     *
     * @param string  $fieldName    Field name
     * @param boolean $processValue Process value flag OPTIONAL
     *
     * @return string
     */
    public function getFieldValue($fieldName, $processValue = false)
    {
        $address = $this->getAddress();

        $methodName = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($fieldName);

        // $methodName assembled from 'get' + camelized $fieldName
        $result = $address->$methodName();

        if ($result && false !== $processValue) {
            switch ($fieldName) {
                case 'state_id':
                    $result = $address->getCountry() && $address->getCountry()->hasStates()
                        ? $address->getState()->getState()
                        : null;
                    break;

                case 'custom_state':
                    $result = $address->getCountry() && $address->getCountry()->hasStates()
                        ? null
                        : $result;
                    break;

                case 'country_code':
                    $result = $address->getCountry()
                        ? $address->getCountry()->getCountry()
                        : null;
                    break;

                case 'type':
                    $result = $address->getTypeName();
                    break;

                default:
            }
        }

        return $result;
    }

    /**
     * Get profile Id
     *
     * @return string
     */
    public function getProfileId()
    {
        return \XLite\Core\Request::getInstance()->profile_id;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->getParam(self::PARAM_DISPLAY_WRAPPER)) {
            $list[] = 'form_field/select_country.js';
        }

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'address/style.css';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Address';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'address/' . $this->getParam(self::PARAM_DISPLAY_MODE);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_DISPLAY_MODE => new \XLite\Model\WidgetParam\TypeString(
                'Display mode', self::DISPLAY_MODE_TEXT, false
            ),
            self::PARAM_ADDRESS => new \XLite\Model\WidgetParam\TypeObject(
                'Address object', new \XLite\Model\Address(), false
            ),
            self::PARAM_DISPLAY_WRAPPER => new \XLite\Model\WidgetParam\TypeBool(
                'Display wrapper', false, false
            ),
        );
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'address/wrapper.twig';
    }

    /**
     * Use body template
     *
     * @return boolean
     */
    protected function useBodyTemplate()
    {
        return !$this->getParam(self::PARAM_DISPLAY_WRAPPER);
    }
}
