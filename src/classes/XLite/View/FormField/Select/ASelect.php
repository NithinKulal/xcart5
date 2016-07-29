<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Form abstract selector
 */
abstract class ASelect extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_OPTIONS = 'options';
    const PARAM_DISABLED = 'disabled';

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
        return 'select.twig';
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
     * Checks if the list is empty
     *
     * @return boolean
     */
    protected function isListEmpty()
    {
        return 0 >= count($this->getOptions());
    }

    /**
     * Check - option is group or not
     *
     * @param mixed $option Option
     *
     * @return boolean
     */
    protected function isGroup($option)
    {
        return is_array($option);
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
            static::PARAM_DISABLED => new \XLite\Model\WidgetParam\TypeBool('Disabled', $this->getDefaultDisableState()),
        );
    }

    /**
     * getDefaultDisableState
     *
     * @return boolean
     */
    protected function getDefaultDisableState()
    {
        return false;
    }

    /**
     * isDisabled
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return $this->getParam(static::PARAM_DISABLED);
    }

    /**
     * Get select specific attributes
     *
     * @return array
     */
    protected function getSelectAttributes()
    {
        $list = array();

        if ($this->isDisabled()) {
            $list['disabled'] = 'disabled';
        }

        return $list;
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return array_merge(parent::getAttributes(), $this->getSelectAttributes());
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

        return $value;
    }


    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isOptionSelected($value)
    {
        return strval($value) === strval($this->getValue());
    }

    /**
     * Check - specified option group is disabled or not
     *
     * @param mixed $optionGroupIndex Option group index
     *
     * @return boolean
     */
    protected function isOptionGroupDisabled($optionGroupIndex)
    {
        return false;
    }

    /**
     * Check - specidifed option is disabled or not
     *
     * @param mixed $value Option value
     *
     * @return boolean
     */
    protected function isOptionDisabled($value)
    {
        return false;
    }

    /**
     * Get option group attributes as HTML code
     *
     * @param mixed $optionGroupIdx Option group index
     * @param array $optionGroup    Option group
     *
     * @return string
     */
    protected function getOptionGroupAttributesCode($optionGroupIdx, array $optionGroup)
    {
        $list = array();

        foreach ($this->getOptionGroupAttributes($optionGroupIdx, $optionGroup) as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }

    /**
     * Get option group attributes
     *
     * @param mixed $optionGroupIdx Option group index
     * @param array $optionGroup    Option group
     *
     * @return array
     */
    protected function getOptionGroupAttributes($optionGroupIdx, array $optionGroup)
    {
        $attributes = array(
            'label' => static::t($optionGroup['label']),
        );

        if ($this->isOptionGroupDisabled($optionGroupIdx)) {
            $attributes['disabled'] = 'disabled';
        }

        return $attributes;
    }

    /**
     * Get option attributes as HTML code
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return string
     */
    protected function getOptionAttributesCode($value, $text)
    {
        $list = array();

        foreach ($this->getOptionAttributes($value, $text) as $name => $value) {
            $list[] = $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return implode(' ', $list);
    }

    /**
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = array(
            'value' => $value,
        );

        if ($this->isOptionSelected($value)) {
            $attributes['selected'] = 'selected';
        }

        if ($this->isOptionDisabled($value)) {
            $attributes['disabled'] = 'disabled';
        }

        return $attributes;
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
