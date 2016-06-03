<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Input;

/**
 * Common checkbox
 */
class TrailingZeroesCheckbox extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * prepareAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);

        $e = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getDefaultCurrency()->getE();

        return $attrs + array(
            'data-e'            => $e,
            'data-thousandpart' => \XLite\View\FormField\Select\FloatFormat::THOUSAND_PART,
            'data-hundredspart' => \XLite\View\FormField\Select\FloatFormat::HUNDRENDS_PART,
            'data-delimiter'    => \XLite\View\FormField\Select\FloatFormat::FORMAT_DELIMITER,
        );
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return 1;
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return \XLite\Core\Config::getInstance()->General->trailing_zeroes;
    }
}