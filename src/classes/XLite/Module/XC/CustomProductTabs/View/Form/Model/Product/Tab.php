<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\Form\Model\Product;

/**
 * Product tabs list search form
 */
class Tab extends \XLite\View\Form\AForm
{

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'UpdateProductTab';
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine product-tab');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return array(
            'tab_id' => \XLite\Core\Request::getInstance()->tab_id,
            'product_id'=>\XLite\Core\Request::getInstance()->product_id,
            'page'=>\XLite\Core\Request::getInstance()->page,
        );
    }

}
