<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text;

/**
 * Date 
 */
class Date Extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/inline/input/text/date.js';

        return $list;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Text\Date';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-date';
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getViewValue(array $field)
    {
        $value = parent::getViewValue($field);

        if (is_scalar($value)) {
            $value = \XLite\Core\Converter::formatDate($value);
        }

        return $value;
    }

}

