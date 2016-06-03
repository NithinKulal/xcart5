<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Popup;

/**
 * Address
 */
abstract class Address extends \XLite\View\FormField\Inline\Popup\APopup
{

    /**
     * @var string|null State runtime cache is case that country without states changed to country with states and vice versa
     */
    protected $oldState = null;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!$this->getViewOnly()) {
            $list[] = 'form_field/select_country.js';
            $list[] = 'form_field/inline/popup/address/controller.js';
        }

        return $list;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Hidden';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-address';
    }

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        $fields = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            $fields[$field->getServiceName()] = array(
                static::FIELD_NAME    => $field->getServiceName(),
                static::FIELD_CLASS   => $this->defineFieldClass(),
            );
        }

        return $fields;
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        $method = 'get' . \Includes\Utils\Converter::convertToCamelCase($field[static::FIELD_NAME]);
        $addressMethod = 'get' . ucfirst($this->getParam(static::PARAM_FIELD_NAME));

        // $method assembled from 'get' + field short name
        return $this->getEntity()->$addressMethod()
            ? $this->getEntity()->$addressMethod()->$method()
            : null;
    }

    /**
     * Get field name parts
     *
     * @param array $field Field
     *
     * @return array
     */
    protected function getNameParts(array $field)
    {
        return array(
            $this->getParam(static::PARAM_FIELD_NAME),
            $field[static::FIELD_NAME],
        );
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/popup/address/view.twig';
    }

    /**
     * Get field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'form_field/inline/popup/address/field.twig';
    }

    /**
     * Get popup parameters
     *
     * @return array
     */
    protected function getPopupParameters()
    {
        $list = parent::getPopupParameters();

        $list['type'] = $this->getParam(static::PARAM_FIELD_NAME);

        return $list;
    }

    /**
     * Get old field entity value
     *
     * @param \XLite\Model\Address  $address    Address
     * @param array                 $field      Field
     * @param mixed                 $value      Value
     *
     * @return mixed
     */
    protected function getOldPropertyValue(\XLite\Model\Address $address, array $field, $value)
    {
        $oldValue = null;

        $getterMethod = 'get' . \XLite\Core\Converter::convertToCamelCase($field['field'][static::FIELD_NAME]);

        if (method_exists($address, $getterMethod)) {
            // Get address property via specific method
            $oldValue = $address->$getterMethod();

        } else {
            // Get address property via common setterProperty() method
            $oldValue = $address->getterProperty($field['field'][static::FIELD_NAME], $value);
        }

        return $oldValue;
    }

    /**
     * Set field entity value
     *
     * @param \XLite\Model\Address  $address    Address
     * @param array                 $field      Field
     * @param mixed                 $value      Value
     *
     * @return mixed
     */
    protected function setPropertyValue(\XLite\Model\Address $address, array $field, $value)
    {
        $setterMethod = $this->getAddressFieldMethodName($field);

        if (method_exists($address, $setterMethod)) {
            // Set address property via specific method
            $address->$setterMethod($value);

        } else {
            // Set address property via common setterProperty() method
            $address->setterProperty($field['field'][static::FIELD_NAME], $value);
        }
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $address = $this->getAddressModel();

        if ($address) {

            $oldValue = $this->getOldPropertyValue($address, $field, $value);

            $this->setPropertyValue($address, $field, $value);

            // Register order changes
            if ($value != $oldValue) {
                $this->registerOrderChanges($address, $field, $value, $oldValue);
            }
        }
    }

    /**
     * Set runtime cache for custom state to state object
     *
     * @param \XLite\Model\Address  $address    Address
     * @param array                 $field      Field
     *
     * @return void
     */
    protected function setOldState(\XLite\Model\Address $address, $field)
    {
        $this->oldState = $this->getOldPropertyValue($address, $field, null);
    }

    /**
     * Register address changes in order history
     * Prepare data to register as an order changes
     *
     * @param array $field      Field
     * @param mixed $value      Value
     * @param mixed $oldValue   Old value
     *
     * @return void
     */
    protected function registerOrderChanges(\XLite\Model\Address $address, array $field, $value, $oldValue)
    {
        $ignoreChange = false;

        $fieldName = $field['field'][static::FIELD_NAME];

        switch ($fieldName) {

            case 'country_code': {
                $fieldName = 'Country';
                $oldCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(array('code' => $oldValue));
                $oldValue = $oldCountry ? $oldCountry->getCountry() : $oldValue;
                $newCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(array('code' => $value));

                $oldHasStates = $oldCountry && $oldCountry->hasStates();
                $newHasStates = $newCountry && $newCountry->hasStates();

                if ($newHasStates && !$oldHasStates) {
                    $customStateField = array(
                        'field' => array(
                            static::FIELD_NAME => 'custom_state'
                        )
                    );
                    $this->setOldState($address, $customStateField);
                }
                if ($oldHasStates && !$newHasStates) {
                    $stateField = array(
                        'field' => array(
                            static::FIELD_NAME => 'state_id'
                        )
                    );
                    $this->setOldState($address, $stateField);
                }

                $value = $newCountry ? $newCountry->getCountry() : $value;
                break;
            }

            case 'state_id': {
                if ($address->getCountry() && $address->getCountry()->hasStates()) {
                    $fieldName = 'State';

                    if (null === $this->oldState) {
                        $oldState = \XLite\Core\Database::getRepo('XLite\Model\State')->find($oldValue);
                        $oldValue = $oldState ? $oldState->getState() : $oldValue;
                    } else {
                        $oldValue = $this->oldState;
                    }

                    $newState = \XLite\Core\Database::getRepo('XLite\Model\State')->find($value);
                    $value = $newState ? $newState->getState() : $value;

                } else {
                    $ignoreChange = true;
                }
                break;
            }

            case 'custom_state': {
                if ($address->getCountry() && $address->getCountry()->hasStates()) {
                    $ignoreChange = true;

                } else {
                    $fieldName = 'State';
                    if (null !== $this->oldState) {
                        $oldState = \XLite\Core\Database::getRepo('XLite\Model\State')->find($this->oldState);
                        $oldValue = $oldState ? $oldState->getState() : $oldValue;
                    }
                }
                break;
            }
        }

        if (!$ignoreChange) {
            \XLite\Controller\Admin\Order::setOrderChanges(
                $this->getParam(static::PARAM_FIELD_NAME) . ':' . $fieldName,
                $value,
                $oldValue
            );
        }
    }

    /**
     * Get address model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressModel()
    {
        // Prepare address getter (getBillingAddress or getShippingAddress)
        $addressMethod = 'get' . \XLite\Core\Converter::convertToCamelCase($this->getParam(static::PARAM_FIELD_NAME));

        return $this->getEntity()->$addressMethod();
    }

    /**
     * Get address field method name 
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getAddressFieldMethodName(array $field)
    {
        return 'set' . \XLite\Core\Converter::convertToCamelCase($field['field'][static::FIELD_NAME]);
    }
}
