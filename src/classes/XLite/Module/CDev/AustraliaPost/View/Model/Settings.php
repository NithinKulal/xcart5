<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AustraliaPost\View\Model;

/**
 * Australia post configuration form model
 */
class Settings extends \XLite\View\Model\AShippingSettings
{
    /**
     * Define form field classes and values
     *
     * @return void
     */
    protected function defineFormFields()
    {
        $config = \XLite\Core\Config::getInstance()->CDev->AustraliaPost;
        if ($config->optionValues) {
            parent::defineFormFields();

        } else {
            $this->formFields = array();
        }
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $options = parent::getEditableOptions();

        foreach ($options as $key => $option) {
            if ($option->getName() === 'optionValues') {
                unset($options[$key]);
            }
        }

        return $options;
    }

    /**
     * Detect form field class by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return string
     */
    protected function detectFormFieldClassByOption(\XLite\Model\Config $option)
    {
        return 'dimensions' === $option->getName()
            ? 'XLite\View\FormField\Input\Text\Dimensions'
            : parent:: detectFormFieldClassByOption($option);
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($option->getName()) {
            case 'api_key':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'test_mode' => array(false),
                    ),
                );
                break;

            case 'dimensions':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'package_box_type' => array('AUS_PARCEL_TYPE_BOXED_OTH'),
                    ),
                );
                break;

            case 'extra_cover_value':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'extra_cover' => array(true),
                    ),
                );
                break;
        }


        return $cell;
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
        $value = parent::getModelObjectValue($name);
        if ('dimensions' === $name) {
            $value = unserialize($value);
        }

        return $value;
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = array();

        $buttons = parent::getFormButtons();
        $submit = $buttons['submit'];

        unset($buttons['submit']);

        $config = \XLite\Core\Config::getInstance()->CDev->AustraliaPost;
        if ($config->optionValues) {
            $result['submit'] = $submit;

            $url = $this->buildURL('aupost', 'renew_settings');
            $result['module_settings'] = new \XLite\View\Button\ProgressState(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL   => 'Renew available settings',
                    \XLite\View\Button\AButton::PARAM_STYLE   => 'action always-enabled',
                    \XLite\View\Button\Regular::PARAM_JS_CODE => 'self.location=\'' . $url . '\'',
                )
            );
        } else {
            $url = $this->buildURL('aupost', 'renew_settings');
            $result['module_settings'] = new \XLite\View\Button\ProgressState(
                array(
                    \XLite\View\Button\AButton::PARAM_LABEL    => 'Get module settings',
                    \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                    \XLite\View\Button\AButton::PARAM_STYLE    => 'action always-enabled',
                    \XLite\View\Button\Regular::PARAM_JS_CODE  => 'self.location=\'' . $url . '\'',
                )
            );
        }

        return $result + $buttons;
    }
}
