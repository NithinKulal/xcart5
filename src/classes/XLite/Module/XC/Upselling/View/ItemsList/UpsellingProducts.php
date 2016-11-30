<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\View\ItemsList;

use XLite\View\CacheableTrait;

/**
 * Related products widget (customer area)
 *
 * @ListChild (list="center.bottom", zone="customer", weight="700")
 */
class UpsellingProducts extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget target
     */
    const WIDGET_TARGET_PRODUCT = 'product';

    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Module\XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_PARENT_PRODUCT_ID => self::PARAM_PRODUCT_ID,
        ];
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET_PRODUCT;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Related products');
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list               = parent::getWidgetParameters();
        $list['product_id'] = $this->getProductId();

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\ObjectId\Product(
                'Product ID',
                \XLite\Core\Request::getInstance()->product_id
            ),
        ];

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_GRID);
        $this->widgetParams[self::PARAM_GRID_COLUMNS]->setValue(5);

        $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]->setValue(false);
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\Upselling\Model\UpsellingProduct';
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = parent::getData($cnd, $countOnly);

        if (!$countOnly) {
            return array_map(function ($item) {
                /** @var \XLite\Module\XC\Upselling\Model\UpsellingProduct $item  */
                return $item->getProduct();
            }, $result);
        }

        return $result;
    }

    /**
     * Get currently viewed product ID
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getParam(self::PARAM_PRODUCT_ID);
    }

    /**
     * Register the widget/request parameters that will be used as the widget cache parameters.
     * In other words changing these parameters by customer effects on widget content
     *
     * @return array
     */
    protected function defineCachedParams()
    {
        return array_merge(parent::defineCachedParams(), [self::PARAM_PRODUCT_ID]);
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' upselling-products';
    }
}
