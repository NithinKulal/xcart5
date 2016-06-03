<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Controller\Admin;

/**
 * \XLite\Module\CDev\FeaturedProducts\Controller\Admin\Categories
 */
class ProductList extends \XLite\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * doActionSearch
     *
     * @return void
     */
    protected function doActionSearchFeaturedProducts()
    {
        $sessionCell    = \XLite\Module\CDev\FeaturedProducts\View\Admin\FeaturedProducts::getSessionCellName();
        $searchParams   = \XLite\View\ItemsList\Model\Product\Admin\Search::getSearchParams();
        $productsSearch = array();
        $cBoxFields     = array(
            \XLite\View\ItemsList\Model\Product\Admin\Search::PARAM_SEARCH_IN_SUBCATS
        );

        foreach ($searchParams as $modelParam => $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $productsSearch[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        foreach ($cBoxFields as $requestParam) {
            $productsSearch[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam)
                ? 1
                : 0;
        }

        \XLite\Core\Session::getInstance()->{$sessionCell} = $productsSearch;
    }
}
