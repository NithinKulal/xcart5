<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList;

/**
 * Select based on radio buttons list
 */
abstract class ARadioButtonsList extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'radio_buttons_list.twig';
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

        $attributes['type'] = 'radio';
        $attributes['name'] = $this->getName();

        return $attributes;
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $result = $value = parent::getValue();
        $options = $this->getOptions();
        if (!(isset($value) && isset($options[$value]))) {
            $value = array_keys($options);
            $result = array_shift($value);
        }

        return $result;
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
