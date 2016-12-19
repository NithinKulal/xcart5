<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\Model;

/**
 * Registration request view model
 */
class Registration extends \XLite\View\Model\AModel
{
    const SCHEMA_FIELD_EMAIL            = 'email';
    const SCHEMA_FIELD_PASSWORD         = 'password';
    const SCHEMA_FIELD_CURRENCY         = 'currency';
    const SCHEMA_FIELD_WAREHOUSE        = 'warehouse';
    const SCHEMA_FIELD_OTHERS_COUNTRY   = 'others_country';

    /**
     * Default form values
     *
     * @var array
     */
    protected $defaultValues;

    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        self::SCHEMA_FIELD_EMAIL => array(
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Input\Text\Email',
            self::SCHEMA_LABEL     => 'Email address to receive Pilibaba payment',
            self::SCHEMA_REQUIRED  => true,
            self::SCHEMA_HELP     => 'Please enter the contact information for the owner of this business or primary contact person for this account',
        ),
        self::SCHEMA_FIELD_PASSWORD => array(
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL     => 'Password',
            self::SCHEMA_REQUIRED  => true,
            self::SCHEMA_HELP     => 'Please enter 8-11 characters',
        ),
        self::SCHEMA_FIELD_CURRENCY => array(
            self::SCHEMA_CLASS     => 'XLite\Module\XC\Pilibaba\View\FormField\Select\Currency',
            self::SCHEMA_LABEL     => 'Primary currency',
            self::SCHEMA_REQUIRED  => true,
            self::SCHEMA_HELP     => 'The currency used on your website and withdraw to your bank account',
        ),
        self::SCHEMA_FIELD_WAREHOUSE => array(
            self::SCHEMA_CLASS     => 'XLite\Module\XC\Pilibaba\View\FormField\Select\Warehouse',
            self::SCHEMA_LABEL     => 'Warehouse',
            self::SCHEMA_REQUIRED  => true,
            self::SCHEMA_HELP     => 'Select the nearest warehouse you will shipping to. When you receive orders from Chinese customers (via Pilibaba gateway) you can deliver parcels to this warehouse.',
        ),
        self::SCHEMA_FIELD_OTHERS_COUNTRY => array(
            self::SCHEMA_CLASS     => 'XLite\View\FormField\Select\Country',
            self::SCHEMA_LABEL     => 'Others',
            self::SCHEMA_REQUIRED  => true,
            self::SCHEMA_HELP     => 'Tell us your country, and we will inform you once warehouse in this country is opened',
            self::SCHEMA_ATTRIBUTES => array('all' => true),
            self::SCHEMA_DEPENDENCY => array(
                self::DEPENDENCY_SHOW => array(
                    self::SCHEMA_FIELD_WAREHOUSE => 'others',
                ),
            ),
        ),
    );

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        return null;
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
        return array(
            self::SCHEMA_FIELD_EMAIL            => \XLite\Core\Auth::getInstance()->getProfile()->getLogin(),
            self::SCHEMA_FIELD_CURRENCY         => \XLite::getInstance()->getCurrency(),
        );
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XC\Pilibaba\View\Form\Registration';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Click to create Pilibaba account',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
    }
}
