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
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::P_VIEWED_PRODUCT_ID => self::PARAM_PRODUCT_ID,
        ];
    }

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
        return static::t('Customers who viewed this product bought');
    }

    /**
     * Define widget parameters
     *
     * @return integer
     */
    protected function getMaxCount()
    {
        return (int) \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cvb_max_count_in_block;
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
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();
        if (!$this->getParam(\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR)) {
            return $this->getPager()->getLimitCondition(
                0,
                $this->getParam(\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT),
                $cnd
            );
        }

        return $cnd;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array|null
     */
    protected function getOrderBy()
    {
        return ['bp.count', static::SORT_ORDER_DESC];
    }
}
