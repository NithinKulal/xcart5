<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Customer;

use XLite\View\CacheableTrait;

/**
 * Featured products widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="300")
 */
class FeaturedProducts extends \XLite\View\ItemsList\Product\Customer\Category\ACategory
{
    use CacheableTrait;

    /**
     * Featured products
     *
     * @var mixed
     */
    protected $featuredProducts;

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue($this->getDisplayMode());
        $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
        $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_COUNT]->setValue(5);
    }

    /**
     * Get widget display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getParam(static::PARAM_IS_EXPORTED)
            ? $this->getParam(static::PARAM_DISPLAY_MODE)
            : \XLite\Core\Config::getInstance()->CDev->FeaturedProducts->featured_products_look;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Featured products';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_GRID_COLUMNS]->setValue(3);

        unset($this->widgetParams[static::PARAM_SHOW_DISPLAY_MODE_SELECTOR]);
        unset($this->widgetParams[static::PARAM_SHOW_SORT_BY_SELECTOR]);
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Condition
     * @param boolean                $countOnly Count only flag
     *
     * @return array
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (null === $this->featuredProducts) {
            $products = array();
            $fp = \XLite\Core\Database::getRepo('XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct')
                ->getFeaturedProducts($this->getCategoryId());

            foreach ($fp as $product) {
                $products[] = $product->getProduct();
            }

            $this->featuredProducts = $products;
        }

        return true === $countOnly
            ? count($this->featuredProducts)
            : $this->featuredProducts;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-featured-products';
    }
}
