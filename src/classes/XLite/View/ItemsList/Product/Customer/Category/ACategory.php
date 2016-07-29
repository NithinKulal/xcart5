<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer\Category;

/**
 * Category products list widget (abstract)
 *
 */
abstract class ACategory extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use \XLite\View\ItemsList\Product\Customer\DefaultSortByTrait;

    /**
     * Widget parameter names
     */
    const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Allowed sort criterions
     */
    const SORT_BY_MODE_DEFAULT = 'cp.orderby';

    /**
     * Widget target
     */
    const WIDGET_TARGET = 'category';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->processAdditionalSortByModes();
    }

    /**
     * Category
     *
     * @var \XLite\Model\Category
     */
    protected $category;

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
        return '\XLite\View\Pager\Customer\Product\Category';
    }

    /**
     * Get requested category object
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        if (!isset($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($this->getCategoryId());
        }

        return $this->category;
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

        $this->widgetParams += array(
            static::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category('Category ID', $this->getRootCategoryId()),
        );
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
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|void
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $category = $this->getCategory();

        return $category ? $category->getProducts($cnd, $countOnly) : null;
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
        return \XLite\View\Pager\APager::PARAM_PAGE_ID != $param ? parent::getSavedRequestParam($param) : null;
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
