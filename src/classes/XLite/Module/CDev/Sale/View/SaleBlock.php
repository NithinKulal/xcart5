<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

use XLite\View\CacheableTrait;

/**
 * Sale products block widget
 *
 * @ListChild (list="sidebar.single", zone="customer", weight="170")
 * @ListChild (list="sidebar.first", zone="customer", weight="130")
 */
class SaleBlock extends \XLite\Module\CDev\Sale\View\ASale
{
    use CacheableTrait;

    /**
     * Widget parameter
     */
    const PARAM_MAX_ITEMS_TO_DISPLAY = 'maxItemsToDisplay';

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
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE],
            $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]
        );

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue($this->getDisplayMode());
    }

    /**
     * Return default display mode from settings
     */
    protected function getDefaultDisplayMode()
    {
        return static::DISPLAY_MODE_STHUMB;
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
                $this->getMaxCountInBlock(),
                true,
                true
            ),
        ];
    }

    /**
     * Returns search products conditions
     *
     * @param \XLite\Core\CommonCell $cnd Initial search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        return $this->postprocessSearchCase(
            parent::getSearchCondition()
        );
    }

    /**
     * Get limit condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        return $this->postprocessSearchCase(
            parent::getLimitCondition()
        );
    }

    /**
     * Returns category condition
     * 
     * @return integer
     */
    protected function getCategoryId()
    {
        return (\XLite\Core\Config::getInstance()->CDev->Sale->sale_in_current_category
            && \XLite::getController()->getCategoryId())
            ? \XLite::getController()->getCategoryId()
            : \XLite::getController()->getRootCategoryId();
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

        $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;

        if ($this->getCategoryId()) {
            $searchCase->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();
        }

        if ($this->getMaxItemsCount()) {
            $searchCase->{\XLite\Model\Repo\Product::P_LIMIT} = [
                0,
                $this->getMaxItemsCount(),
            ];
        }

        return $searchCase;
    }

    /**
     * Returns maximum allowed items count
     *
     * @return integer
     */
    protected function getMaxItemsCount()
    {
        return $this->getParam(self::PARAM_MAX_ITEMS_TO_DISPLAY) ?: $this->getMaxCountInBlock();
    }

    /**
     * getSidebarMaxItems
     *
     * @return integer
     */
    protected function getSidebarMaxItems()
    {
        return $this->getMaxItemsCount();
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
        return parent::isVisible()
        && \XLite\Core\Config::getInstance()->CDev->Sale->sale_enabled
        && static::getWidgetTarget() !== \XLite\Core\Request::getInstance()->target
        && 0 < $this->getItemsCount();
    }

    /**
     * Get 'More...' link URL for Sale products list
     *
     * @return string
     */
    protected function getMoreLinkURL()
    {
        return $this->buildURL(self::WIDGET_TARGET_SALE_PRODUCTS);
    }

    /**
     * Get 'More...' link text for Sale products list
     *
     * @return string
     */
    protected function getMoreLinkText()
    {
        return static::t('All products on sale');
    }

    /**
     * Check status of 'More...' link for sidebar list
     *
     * @return boolean
     */
    protected function isShowMoreLink()
    {
        return true;
    }
}
