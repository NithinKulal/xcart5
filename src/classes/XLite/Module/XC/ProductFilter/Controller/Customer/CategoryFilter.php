<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Customer;

/**
 * Category filter
 *
 */
class CategoryFilter extends \XLite\Controller\Customer\Category
{
    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && 1 < $this->getCategory()->getProductsCount();
    }

    /**
     * Do action filter
     *
     * @return void
     */
    protected function doActionFilter()
    {
        $sessionCell = $this->isAJAX()
            ? \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter::getSessionCellName()
            : \XLite\View\ItemsList\Product\Customer\Category\Main::getSessionCellName();

        $data = \XLite\Core\Session::getInstance()->$sessionCell;

        if (!is_array($data)) {
            $data = array();
        }

        $data['filter'] = \XLite\Core\Request::getInstance()->filter;

        $filters = \Includes\Utils\ArrayManager::filterMultidimensional(is_array($data['filter']) ? $data['filter'] : []);

        if (!$this->isAJAX()) {
            $sessionCell = \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter::getSessionCellName();
        }

        \XLite\Core\Session::getInstance()->$sessionCell = $data;

        $returnUrl = $this->buildURL(
            'category_filter',
            '',
            array('category_id' => \XLite\Core\Request::getInstance()->category_id)
        );

        if ($filters) {
            $returnUrl .= '#' . urldecode(http_build_query(
                array('filter' => $filters)
            ));
        }

        $this->setReturnURL($returnUrl);
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return boolean
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return false;
    }
}
