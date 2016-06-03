<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox;

/**
 * Simple checkbox
 */
class Simple extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Checkbox\SimpleInline';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-simple-checkbox';
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        return parent::getFieldParams($field) + array(
            \XLite\View\FormField\Input\Checkbox::PARAM_IS_CHECKED => $this->getEntityValue(),
        );
    }

    /**
     * Preprocess value before save: return 1 or 0
     *
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function preprocessValueBeforeSave($value)
    {
        return intval($value);
    }

    /**
     * Preprocess value forsave
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function preprocessSavedValue($value)
    {
        return (bool)$value;
    }

    /**
     * Check - field has view or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }
}

