<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model\DataSource;

/**
 * Abstract data source model widget
 */
abstract class ADataSource extends \XLite\View\Model\AModel
{
    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        // If field name starts with 'parameter_', this field
        // is represented by a bound \XLite\Model\DataSource\Parameter object
        if (strpos($name, 'parameter_') === 0) {
            $paramName = substr($name, 10);

            $value = $this->getModelObject()->getParameterValue($paramName);

        } else {
            // Otherwise it's a field of a current model object
            $value = parent::getModelObjectValue($name);
        }

        return $value;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        foreach ($data as $name => $value) {
            if (strpos($name, 'parameter_') === 0) {
                $paramName = substr($name, 10);

                $this->getModelObject()->setParameterValue($paramName, $value);

                // Remove already set properties
                unset($data[$name]);
            }
        }

        parent::setModelProperties($data);
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
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Submit',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action',
            )
        );

        return $result;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\DataSource';
    }
}
