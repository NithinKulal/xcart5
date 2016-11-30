<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer\Category;

/**
 * Category products list widget (abstract)
 */
abstract class ACategory extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use \XLite\View\ItemsList\Product\Customer\DefaultSortByTrait;

    /**
     * Widget parameter names
     */
    const PARAM_CATEGORY_ID  = 'category_id';

    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_DEFAULT = 'cp.orderby';

    /**
     * Widget target
     */
    const WIDGET_TARGET = 'category';

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::P_CATEGORY_ID => static::PARAM_CATEGORY_ID,
        ];
    }

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->processAdditionalSortByModes();
    }

    /**
     * Get products single order 'sort by' fields
     * Return in format [sort_by_field => sort_order]
     *
     * @return array
     */
    protected function getSingleOrderSortByFields()
    {
        return parent::getSingleOrderSortByFields() + [
            static::SORT_BY_MODE_DEFAULT => static::SORT_ORDER_DESC
        ];
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'main';

        return $result;
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' category-products';
    }


    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Customer\Product\Category';
    }

    /**
     * Get requested category object
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        return $this->executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($this->getCategoryId());
        });
    }

    /**
     * Get requested category ID
     *
     * @return integer
     */
    protected function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->{static::PARAM_CATEGORY_ID}
            ?: $this->getRootCategoryId();
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
            static::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category('Category ID', $this->getRootCategoryId()),
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_CATEGORY_ID;
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);
        if ('directLink' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
            && !\XLite::isAdminZone()
        ) {
            $searchCase->{\XLite\Model\Repo\Product::P_INVENTORY} = false;
        }

        return $searchCase;
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list['category_id'] = \XLite\Core\Request::getInstance()->category_id;

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategory() && $this->getCategory()->isVisible();
    }

    /**
     * Unset 'pageId' value from saved parameters
     *
     * @param string $param Parameter name
     *
     * @return mixed
     */
    protected function getSavedRequestParam($param)
    {
        return \XLite\View\Pager\APager::PARAM_PAGE_ID !== $param ? parent::getSavedRequestParam($param) : null;
    }

    // {{{ Cache

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->getCategoryId();

        return $list;
    }

    // }}}

    /**
     * Defines if the widget is listening to #hash changes
     *
     * @return boolean
     */
    protected function getListenToHash()
    {
        return true;
    }

    /**
     * Defines the #hash prefix of the data for the widget
     * @TODO implement!
     *
     * @return string
     */
    protected function getListenToHashPrefix()
    {
        return 'product.category';
    }
}
