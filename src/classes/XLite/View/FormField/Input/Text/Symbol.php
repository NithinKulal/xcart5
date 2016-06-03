<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Input with symbol
 */
class Symbol extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Widget param names
     */
    const PARAM_SYMBOL      = 'symbol';
    const PARAM_SYMBOL_TYPE = 'symbolType';

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        $additional = ' with-' .$this->getSymbolType();

        return trim(parent::getWrapperClass() . ' input-text-symbol ' . $additional);
    }

    /**
     * Return symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->getParam(static::PARAM_SYMBOL);
    }

    /**
     * Return symbol type
     *
     * @return string
     */
    public function getSymbolType()
    {
        return $this->getParam(static::PARAM_SYMBOL_TYPE);
    }

    /**
     * Return default symbol type
     *
     * @return string
     */
    public function getDefaultSymbolType()
    {
        return 'prefix';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/symbol.twig';
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
            static::PARAM_SYMBOL        => new \XLite\Model\WidgetParam\TypeString('Symbol', ''),
            static::PARAM_SYMBOL_TYPE   => new \XLite\Model\WidgetParam\TypeString(
                'Symbol',
                $this->getDefaultSymbolType()
            ),
        );
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'symbol';

        return $classes;
    }
}
