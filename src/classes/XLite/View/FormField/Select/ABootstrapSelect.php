<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Form abstract bootstrap-based selector
 */
abstract class ABootstrapSelect extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_OPTIONS = 'options';

    /**
     * Register CSS class to use for wrapper block of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' input-bootstrap-select';
    }
    
    /**
     * Return default options list
     *
     * @return array
     */
    abstract protected function getDefaultOptions();

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_SELECT;
    }

    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_object($value) && $value instanceof \XLite\Model\AEntity) {
            $value = $value->getUniqueIdentifier();
        }

        parent::setValue($value);
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'bootstrap_select.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/select';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/bootstrap_select.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/bootstrap_select.js';

        return $list;
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->getParam(self::PARAM_OPTIONS);
    }

    /**
     * Returns label for option
     *
     * @param $option
     *
     * @return string
     */
    protected function getOptionLabel($option = null)
    {
        if (null === $option) {
            $option = $this->getValue() ?: $this->getDefaultValue();
        }
        $options = $this->getOptions();

        return isset($options[$option]) ? $options[$option] : '';
    }

    /**
     * Checks if the list is empty
     *
     * @return boolean
     */
    protected function isListEmpty()
    {
        return 0 >= count($this->getOptions());
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_OPTIONS => new \XLite\Model\WidgetParam\TypeCollection(
                'Options', $this->getDefaultOptions(), false
            ),
        );
    }

    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        $value = parent::getDefaultValue();

        if (is_object($value) && $value instanceof \XLite\Model\AEntity) {
            $value = $value->getUniqueIdentifier();
        }

        if (null === $value) {
            $options = $this->getOptions() ?: $this->getDefaultOptions();

            if (is_array($options)) {
                $options = array_keys($options);
                $value = array_shift($options);
            }
        }

        return $value;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array();
    }
}