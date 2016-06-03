<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * 'Customers who viewed this product bought' widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="800")
 */
class ViewedBought extends \XLite\Module\CDev\ProductAdvisor\View\ABought
{
    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' viewed-bought-products';
    }


    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return \XLite\Core\Translation::getInstance()->translate('Customers who viewed this product bought');
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function getMaxCount()
    {
        return intval(\XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cvb_max_count_in_block);
    }

    /**
     * Returns true if block is enabled
     *
     * @return boolean
     */
    protected function isBlockEnabled()
    {
        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cvb_enabled;
    }

    /**
     * Return params list to use for search
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_VIEWED_PRODUCT_ID} = $this->getProductId();

        if (!$this->getParam(self::PARAM_SHOW_SORT_BY_SELECTOR)) {
            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_ORDER_BY} = array('bp.count', 'DESC');
        }

        if (!$this->getParam(\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR)) {
            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_LIMIT}
                = array(0, $this->getParam(\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT));
        }

        return $cnd;
    }
}
