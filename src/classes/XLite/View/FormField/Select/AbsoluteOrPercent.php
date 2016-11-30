<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Absolute or Percent
 */
class AbsoluteOrPercent extends \XLite\View\FormField\Select\ABootstrapSelect
{
    const TYPE_ABSOLUTE = 'a';
    const TYPE_PERCENT = 'p';

    /**
     * Register CSS class to use for wrapper block of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' select-absolute-or-percent';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/absolute_or_percent.css';

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::TYPE_ABSOLUTE => $this->getAbsoluteTypeLabel(),
            static::TYPE_PERCENT => $this->getPercentTypeLabel(),
        );
    }

    /**
     * Return label for TYPE_ABSOLUTE
     *
     * @return string
     */
    public function getAbsoluteTypeLabel()
    {
        $currency = \XLite::getInstance()->getCurrency();

        $result = $currency->getPrefix() ?: $currency->getSuffix();

        return $result ?: $currency->getCode();
    }

    /**
     * Return label for TYPE_PERCENT
     *
     * @return string
     */
    public function getPercentTypeLabel()
    {
        return '%';
    }
}