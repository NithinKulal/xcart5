<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * Select based on checkbox list
 */
abstract class Simple extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'checkbox_list.twig';
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $result = parent::getAttributes();
        if (isset($result['name'])) {
            unset($result['name']);
        }

        return $result;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $result = parent::setCommonAttributes($attrs);
        if (isset($result['multiple'])) {
            unset($result['multiple']);
        }

        return $result;
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
        $attributes = parent::getOptionAttributes($value, $text);
        if ($this->isOptionSelected($value)) {
            $attributes['checked'] = 'checked';
        }

        if (isset($attributes['selected'])) {
            unset($attributes['selected']);
        }

        $attributes['type'] = 'checkbox';
        $attributes['name'] = $this->getName() . '[' . $value . ']';

        $fieldAttributes = $this->getAttributes();
        if (!empty($fieldAttributes['disabled'])) {
            $attributes['disabled'] = 'disabled';
        }

        return $attributes;
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return false;
    }
}
