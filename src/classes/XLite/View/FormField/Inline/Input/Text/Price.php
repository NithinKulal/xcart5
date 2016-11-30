<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/inline/input/text/price.js';

        return $list;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Text\Price';
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
        $sign = 0 <= $value ? '' : '&minus;';

        $valueProcessed = $sign . $field['widget']->getCurrency()->formatValue(abs($value));

        if (!doubleval($value) && $this->isDashed($field)) {
            $valueProcessed = '&mdash;';
        }

        return $valueProcessed;
    }


    /**
     * Is symbol visible
     *
     * @param  array   $field   Field
     * @return boolean
     */
    public function isSymbolVisible(array $field)
    {
        return $field['widget']->getValue() || !$this->isDashed($field);
    }

    /**
     * Is dashed
     *
     * @param  array   $field   Field
     * @return boolean
     */
    public function isDashed(array $field)
    {
        return isset($field['widget']) && $field['widget']->dashed;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-price';
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/input/text/price.twig';
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return $this->getSingleFieldAsWidget()->getCurrency();
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
        return parent::getFieldParams($field)
            + array(\XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_ICON => false);
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        return doubleval(parent::getFieldEntityValue($field));
    }
}
