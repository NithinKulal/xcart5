<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * \XLite\View\CurrencySelect
 */
class CurrencySelect extends \XLite\View\FormField
{
    /**
     * Widget param names
     */

    const PARAM_ALL        = 'all';
    const PARAM_FIELD_NAME = 'field';
    const PARAM_CURRENCY   = 'currency';
    const PARAM_FIELD_ID   = 'fieldId';
    const PARAM_CLASS_NAME = 'className';


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/select_currency.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ALL        => new \XLite\Model\WidgetParam\TypeBool('All', true),
            self::PARAM_FIELD_NAME => new \XLite\Model\WidgetParam\TypeString('Field name', ''),
            self::PARAM_FIELD_ID   => new \XLite\Model\WidgetParam\TypeString('Field ID', ''),
            self::PARAM_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Class name', ''),
            self::PARAM_CURRENCY   => new \XLite\Model\WidgetParam\TypeInt('Value', 840)
        );
    }

    /**
     * Check - display used only currency or all
     *
     * @return boolean
     */
    protected function usedOnly()
    {
        return !$this->getParam(self::PARAM_ALL);
    }

    /**
     * Return currencies list
     *
     * @return array
     */
    protected function getCurrencies()
    {
        return $this->usedOnly()
            ? \XLite\Core\Database::getRepo('XLite\Model\Currency')->findUsed()
            : \XLite\Core\Database::getRepo('XLite\Model\Currency')->findAllSortedByName();
    }
}
