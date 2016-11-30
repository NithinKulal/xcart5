<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Filter;

/**
 * Abstract filter widget
 *
 */
abstract class AFilter extends \XLite\View\AView
{
    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Get filter values
     *
     * @return array
     */
    protected function getFilterValues()
    {
        $filterValues = array();

        if (in_array($this->getTarget(), array('category', 'category_filter'), true)) {
            $filterValues = \XLite\Core\Request::getInstance()->filter ?: array();
        }

        return $filterValues;
    }
}
