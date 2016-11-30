<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

use XLite\View\CacheableTrait;

/**
 * Recently viewed products widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="500")
 */
class RecentlyViewed extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget parameter
     */
    const PARAM_MAX_ITEMS_TO_DISPLAY = 'maxItemsToDisplay';
    const PARAM_PRODUCT_IDS          = 'productIds';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'main';
        $result[] = 'category';

        return $result;
    }

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getWidgetType() === static::WIDGET_TYPE_SIDEBAR
            ? static::DISPLAY_MODE_TEXTS
            : static::DISPLAY_MODE_GRID;
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.recently';
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' recently-viewed-products' . ($this->getWidgetType() === static::WIDGET_TYPE_CENTER ? ' center' : '');
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/CDev/ProductAdvisor/products.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

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

        unset(
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR],
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]
        );
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Recently viewed');
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
            self::PARAM_MAX_ITEMS_TO_DISPLAY => new \XLite\Model\WidgetParam\TypeInt(
                'Maximum products to display',
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->rv_max_count_in_block,
                true,
                true
            ),
            self::PARAM_PRODUCT_IDS          => new \XLite\Model\WidgetParam\TypeCollection(
                'ProductIds',
                \XLite\Module\CDev\ProductAdvisor\Main::getProductIds()
            ),
        ];

        if (\XLite\Core\Layout::getInstance()->isSidebarFirstVisible()) {
            $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_SIDEBAR);
        } else {
            $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        }
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_TEXTS);

        unset(
            $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR],
            $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]
        );
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\Module\CDev\ProductAdvisor\View\Pager\Pager';
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

        $searchCase->{\XLite\Model\Repo\Product::P_PRODUCT_IDS} = $this->getParam(self::PARAM_PRODUCT_IDS);

        return $searchCase;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param bool                   $countOnly
     *
     * @return array|int
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if ($cnd->{\XLite\Model\Repo\Product::P_PRODUCT_IDS}) {

            return parent::getData($cnd, $countOnly);

        } else {

            return $countOnly ? 0 : [];
        }
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();

        return $this->getPager()->getLimitCondition(
            0,
            $this->getParam(self::PARAM_MAX_ITEMS_TO_DISPLAY),
            $cnd
        );
    }

    /**
     * getSidebarMaxItems
     *
     * @return integer
     */
    protected function getSidebarMaxItems()
    {
        return $this->getParam(self::PARAM_MAX_ITEMS_TO_DISPLAY);
    }

    /**
     * Return template of New arrivals widget. It depends on widget type:
     * SIDEBAR/CENTER and so on.
     *
     * @return string
     */
    protected function getTemplate()
    {
        $template = parent::getTemplate();

        if ($template === $this->getDefaultTemplate()
            && self::WIDGET_TYPE_SIDEBAR === $this->getWidgetType()
        ) {
            $template = self::TEMPLATE_SIDEBAR;
        }

        return $template;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->rv_enabled
        && $this->getParam(self::PARAM_PRODUCT_IDS)
        && parent::isVisible();
    }

    /**
     * Get product list item template.
     *
     * @return string
     */
    public function getProductTemplate()
    {
        return $this->getWidgetType() === static::WIDGET_TYPE_CENTER
            ? 'modules/CDev/ProductAdvisor/product.twig'
            : parent::getProductTemplate();
    }

    /**
     * Register the widget/request parameters that will be used as the widget cache parameters.
     * In other words changing these parameters by customer effects on widget content
     *
     * @return array
     */
    protected function defineCachedParams()
    {
        return array_merge(parent::defineCachedParams(), [self::PARAM_PRODUCT_IDS]);
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-recently-viewed';
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        $result = parent::getProductWidgetParams($product);

        return array_merge($result, [
            \XLite\View\Product\ListItem::PARAM_ICON_MAX_HEIGHT => 120,
            \XLite\View\Product\ListItem::PARAM_ICON_MAX_WIDTH  => 100,
        ]);
    }
}
