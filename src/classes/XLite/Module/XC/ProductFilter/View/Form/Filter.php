<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Form;

/**
 * Filter form
 *
 */
class Filter extends \XLite\View\Form\AForm
{

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'category_filter';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'filter';
    }

    /**
     * Get default params
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        $result['category_id'] = $this->getCategory()->getCategoryId();

        return $result;
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'filter-form';
    }

}
