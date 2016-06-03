<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Confirm with password view model
 */
class ConfirmWithPassword extends \XLite\View\Model\AModel
{
    /**
     * Schema default
     *
     * @var array
     */
    protected $schemaDefault = array(
        'password' => array(
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Password',
            self::SCHEMA_LABEL    => 'Password',
            self::SCHEMA_REQUIRED => true,
        ),
    );

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\Notification
     */
    protected function getDefaultModelObject()
    {
        return null;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Model\ConfirmWithPassword';
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
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Confirm',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        $result['cancel'] = new \XLite\View\Button\Regular(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => 'Cancel',
                \XLite\View\Button\AButton::PARAM_STYLE => 'action always-enabled',
            )
        );

        return $result;
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
        return '';
    }
}
