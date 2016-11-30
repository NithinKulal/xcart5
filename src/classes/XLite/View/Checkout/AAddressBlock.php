<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout;
use XLite\View\Model\AModel;

/**
 * Address block info
 */
abstract class AAddressBlock extends \XLite\View\AView
{
    /**
     * RuntimeCache
     *
     * @var array
     */
    protected $addressSchemaFields;

    /**
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    abstract protected function getAddressInfo();

    /**
     * Check - email field is visible or not
     *
     * @return boolean
     */
    abstract protected function isEmailVisible();

    /**
     * Check - password field is visible or not
     *
     * @return boolean
     */
    abstract protected function isPasswordVisible();

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/steps/shipping/parts/address.js';

        return $list;
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
        $result = '';

        $address = $this->getAddressInfo();

        if ($this->getCart() && $this->getCart()->getProfile() && 'email' == $fieldName) {
            $result = $this->getCart()->getProfile()->getLogin();

        } elseif ($this->getCart() && $this->getCart()->getProfile() && 'password' == $fieldName) {
            $result = \XLite\Core\Session::getInstance()->createProfilePassword;

        } elseif (isset($address)) {

            $methodName = 'get' . \XLite\Core\Converter::getInstance()->convertToCamelCase($fieldName);

            // $methodName assembled from 'get' + camelized $fieldName
            $result = $address->$methodName(true);

            if ($result && false !== $processValue) {

                switch ($fieldName) {

                    case 'state_id':
                        $result = $address->getCountry()->hasStates()
                            ? $address->getState()->getState()
                            : null;
                        break;

                    case 'custom_state':
                        $result = $address->getCountry()->hasStates()
                            ? null
                            : $result;
                        break;

                    case 'country_code':
                        $result = $address->getCountry()->getCountry();
                        break;

                    case 'type':
                        $result = $address->getTypeName();
                        break;

                    default:
                }
            }

        } elseif (in_array($fieldName, array('country_code', 'state_id', 'custom_state', 'zipcode'))) {

            $result = \XLite\Model\Address::getDefaultFieldPlainValue($fieldName);
        }

        return $result;
    }

    /**
     * Get an array of address fields
     *
     * @return array
     */
    protected function getAddressFields()
    {
        $result = \XLite::getController()->getAddressFields();

        if ($this->isEmailVisible()) {
            $result['email'] = array(
                \XLite\View\Model\Address\Address::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Text\CheckoutEmail',
                \XLite\View\Model\Address\Address::SCHEMA_LABEL            => 'Email',
                \XLite\View\Model\Address\Address::SCHEMA_REQUIRED         => true,
                \XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES => array(
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                ),
                \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS      => 'address-email',
                \XLite\View\FormField\AFormField::PARAM_COMMENT            => static::t('Your order details will be sent to your e-mail address'),
                \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES         => array(
                    'class' => 'progress-mark-owner',
                ),
                'additionalClass'                                          => $this->getEmailClassName(),
            );
        }

        if ($this->isPasswordVisible()) {
            $result['password'] = array(
                \XLite\View\Model\Address\Address::SCHEMA_CLASS            => 'XLite\View\FormField\Input\PasswordVisible',
                \XLite\View\Model\Address\Address::SCHEMA_LABEL            => 'Password',
                \XLite\View\Model\Address\Address::SCHEMA_REQUIRED         => true,
                \XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES => array(
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                ),
                \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS      => 'password',
                'additionalClass'                                          => $this->getPasswordClassName(),
            );
        }

        return $result;
    }

    /**
     * Add widget to all fields
     *
     * @param array $fields List of fields data
     *
     * @return array
     */
    protected function getAddressSchemaFields()
    {
        if (!isset($this->addressSchemaFields)) {
            $this->addressSchemaFields = $this->getAddressFields();

            foreach ($this->addressSchemaFields as $name => $data) {
                $field = $this->getFieldBySchema($name, $data);
                if ($field) {
                    $this->addressSchemaFields[$name]['widget'] = $field;
                }
            }
        }

        return $this->addressSchemaFields;
    }

    /**
     * Create field widget
     *
     * @param string $name Field name
     * @param array  $data Field data
     *
     * @return \XLite\View\FormField\AFormField
     */
    protected function getFieldBySchema($name, array $data)
    {
        $result = null;

        $class = $data[\XLite\View\Model\Address\Address::SCHEMA_CLASS];

        if (\XLite\Core\Operator::isClassExists($class)) {
            $method = 'prepareFieldParams' . \XLite\Core\Converter::convertToCamelCase($name);

            if (method_exists($this, $method)) {
                $data = $this->$method($data);
            }

            $result = new $class($this->getFieldSchemaArgs($name, $data));
        }

        return $result;
    }

    /**
     * Enable 'Select one' option for state_id selector
     *
     * @param array $data Array of field data
     *
     * @return array
     */
    protected function prepareFieldParamsStateId($data)
    {
        $data[\XLite\View\FormField\Select\State::PARAM_SELECT_ONE] = true;

        return $data;
    }

    /**
     * Prepare field arguments to create form field widget
     *
     * @param string $name Field name
     * @param array  $data Field data
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $data[\XLite\View\Model\Address\Address::SCHEMA_NAME] = $this->getFieldFullName($name);

        $data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES]
            = !empty($data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES])
                ? $data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES]
                : array();

        $data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES] = array_merge(
            $data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES],
            $this->getFieldAttributes($name, $data)
        );

        $data[\XLite\View\Model\Address\Address::SCHEMA_ATTRIBUTES] +=
            isset($data[\XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES])
            ? $data[\XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES]
            : array();

        $data[\XLite\View\Model\Address\Address::SCHEMA_VALUE] = $this->getFieldValue($name);
        $data[\XLite\View\Model\Address\Address::SCHEMA_PLACEHOLDER] = $this->getFieldPlaceholder($name);

        return $data;
    }

    /**
     * Add CSS classes to the list of attributes
     *
     * @param string $fieldName Field service name
     * @param array  $fieldData Array of field properties (see getAddressFields() for the details)
     *
     * @return array
     */
    public function getFieldAttributes($fieldName, array $fieldData)
    {
        $classes = array('field-' . $fieldName);

        if (!empty($fieldData[\XLite\View\Model\Address\Address::SCHEMA_REQUIRED])) {
            $classes[] = 'field-required';
        }

        $attrs = empty($fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES])
            ? array()
            : $fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES];

        if (!isset($attrs['class'])) {
            $attrs['class'] = '';
        }

        $attrs['class'] = trim($attrs['class'] . ' ' . implode(' ', $classes));

        return $attrs;
    }

    /**
     * Get field name
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldFullName($name)
    {
        return $name;
    }

    /**
     * Get field placeholder
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldPlaceholder($name)
    {
        switch ($name) {
            case 'firstname':
                $result = static::t('Your first name');
                break;

            case 'lastname':
                $result = static::t('Your last name');
                break;

            case 'street':
                $result = static::t('1000 Example street');
                break;

            case 'city':
                $result = static::t('Example city');
                break;

            case 'custom_state':
                $result = static::t('Your state');
                break;

            case 'zipcode':
                $result = static::t('90001');
                break;

            case 'phone':
                $result = static::t('+12130000000');
                break;

            case 'email':
                $result = static::t('email@example.com');
                break;

            default:
                $result = '';
        }

        return $result;
    }

    /**
     * Check - display Address book button or not
     *
     * @return boolean
     */
    protected function isDisplayAddressButton()
    {
        return !$this->isAnonymous()
            && $this->getCart()->getProfile()
            && 0 < count($this->getCart()->getProfile()->getAddresses());
    }

    /**
     * Check - create profile flag is enabled or not
     *
     * @return boolean
     */
    protected function isCreateProfile()
    {
        return \XLite\Core\Session::getInstance()->order_create_profile;
    }

    /**
     * Check - display Address book button or not
     *
     * @return boolean
     */
    protected function isSaveNewField()
    {
        return !$this->isAnonymous()
            && $this->getCart()->getProfile()
            && 0 < count($this->getCart()->getProfile()->getAddresses());
    }

    /**
     * Chekc - allow display create profile checkbox or not
     *
     * @return boolean
     */
    protected function isAllowCreateProfile()
    {
        return $this->isAnonymous()
            && $this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getLogin()
            && \XLite\Core\Session::getInstance()->lastLoginUnique;
    }

    /**
     * Check - display create profile warning or not
     *
     * @return boolean
     */
    protected function isDisplayCreateProfileWarning()
    {
        return $this->isAnonymous()
            && $this->getCart()->getProfile()
            && $this->getCart()->getProfile()->getLogin()
            && !\XLite\Core\Session::getInstance()->lastLoginUnique;
    }

    /**
     * Get email field class name
     *
     * @return string
     */
    protected function getEmailClassName()
    {
        $classes = array();

        if ($this->isAllowCreateProfile()) {
            $classes[] = 'allow-create-profile';

        } elseif ($this->isDisplayCreateProfileWarning()) {
            $classes[] = 'create-profile-warning';

        }

        return implode(' ', $classes);
    }

    /**
     * Get password field class name
     *
     * @return string
     */
    protected function getPasswordClassName()
    {
        $classes = array();

        if (!$this->isAllowCreateProfile() || !\XLite\Core\Session::getInstance()->order_create_profile) {
            $classes[] = 'hidden';
        }

        return implode(' ', $classes);
    }

    /**
     * Get field commented data
     *
     * @param array $filedData Field data
     *
     * @return array
     */
    protected function getFieldCommentedData($filedData)
    {
        $commentedData = array();

        if (isset($filedData[AModel::SCHEMA_DEPENDENCY])) {
            $commentedData['dependency'] = $filedData[AModel::SCHEMA_DEPENDENCY];
        }

        return $commentedData;
    }
}
