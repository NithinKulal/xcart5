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
 * @ListChild (list="sidebar.single", zone="customer", weight="180")
 * @ListChild (list="sidebar.second", zone="customer", weight="130")
 */
class RecentlyViewed extends \XLite\View\ItemsList\Product\Customer\ACustomer
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
        $result = parent::getAllowedTargets();

        $result[] = 'main';
        $result[] = 'category';

        return $result;
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' recently-viewed-products';
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

        unset($this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]);
        unset($this->widgetParams[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE]);
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return \XLite\Core\Translation::getInstance()->translate('Recently viewed');
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
            self::PARAM_MAX_ITEMS_TO_DISPLAY => new \XLite\Model\WidgetParam\TypeInt(
                'Maximum products to display',
                $this->getMaxCountInBlock(),
                true,
                true
            ),
        );

        if (\XLite\Core\Layout::getInstance()->isSidebarFirstVisible()) {
            $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_SIDEBAR);
        }
        else{
            $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        }
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_TEXTS);

        unset($this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]);
        unset($this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]);
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\CDev\ProductAdvisor\View\Pager\Pager';
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
        $productIds = \XLite\Module\CDev\ProductAdvisor\Main::getProductIds();

        $maxCount = $this->getMaxItemsCount();

        if ($countOnly) {
            // Calculate count recently viewed products w/o gathering them from database
            $result = min(
                \XLite\Core\Database::getRepo('XLite\Model\Product')->findByProductIds($productIds, true),
                $maxCount
            );

        } else {
            if (count($productIds) > $maxCount) {
                // Cut productIds array up to the limit
                array_splice($productIds, $maxCount);
            }
            $products = $result = array();
            // Get products list
            foreach (
                \XLite\Core\Database::getRepo('XLite\Model\Product')->findByProductIds($productIds, false) as $product
            ) {
                $products[$product->getProductId()] = $product;
            }
            // Sort out products list by its order in the productIds array
            foreach ($productIds as $productId) {
                if (isset($products[$productId])) {
                    $result[] = $products[$productId];
                }
            }
            unset($products);
        }

        return $result;
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
     * Returns maximum allowed items count
     *
     * @return integer
     */
    protected function getMaxCountInBlock()
    {
        return intval(\XLite\Core\Config::getInstance()->CDev->ProductAdvisor->rv_max_count_in_block);
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

        if ($template == $this->getDefaultTemplate()
            && self::WIDGET_TYPE_SIDEBAR == $this->getParam(self::PARAM_WIDGET_TYPE)
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
            && \XLite\Module\CDev\ProductAdvisor\Main::getProductIds()
            && parent::isVisible();
    }

    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = implode(',', \XLite\Module\CDev\ProductAdvisor\Main::getProductIds());

        return $params;
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
}
