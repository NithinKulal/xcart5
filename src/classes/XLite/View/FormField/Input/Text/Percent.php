<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Percent
 */
class Percent extends \XLite\View\FormField\Input\Text\Symbol
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/percent.css';

        return $list;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return '%';
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
        $classes[] = 'percent';

        return $classes;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();
        $attributes['value'] = $this->formatValue($attributes['value']);

        return $attributes;
    }

    /**
     * Format value
     *
     * @param float $value Value
     *
     * @return string
     */
    protected function formatValue($value)
    {
        return number_format(
            round($value, $this->getE()),
            $this->getE(),
            '.',
            ''
        );
    }

    /**
     * Get mantis
     *
     * @return integer
     */
    protected function getE()
    {
        return 0;
    }
}
