<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Form\ItemsList\FeaturedProduct;

/**
 * F products list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'featured_products';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'updateItemsList';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['itemsList'] = '\XLite\Module\CDev\FeaturedProducts\View\ItemsList\Model\FeaturedProduct';
        $list[\XLite\Module\CDev\FeaturedProducts\View\ItemsList\Model\FeaturedProduct::PARAM_CATEGORY_ID]
            = \XLite\Core\Request::getInstance()->id;

        return $list;
    }
}
