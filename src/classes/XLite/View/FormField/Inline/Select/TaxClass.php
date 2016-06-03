<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select;

/**
 * Tax class inline selector
 */
class TaxClass extends \XLite\View\Taxes\Inline\TaxClass
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\TaxClass';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-tax-class';
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
        $result = static::t('Default tax class');

        if ($field['widget']->getValue()) {
            $result = $this->getEntity()->getTaxClass()->getName();
        }

        return $result;
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return true;
    }
}
