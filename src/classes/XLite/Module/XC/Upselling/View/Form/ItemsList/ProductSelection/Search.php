<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Search extends \XLite\View\Form\ItemsList\ProductSelection\Search
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'u_product_selections';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list[\XLite\Module\XC\Upselling\View\ItemsList\Model\UpsellingProduct::PARAM_PARENT_PRODUCT_ID]
            = \XLite\Core\Request::getInstance()->parent_product_id;

        return $list;
    }
}
