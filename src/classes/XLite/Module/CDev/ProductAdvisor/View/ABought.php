<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

use XLite\View\CacheableTrait;

/**
 * Abstract widget class for widgets 'Customers who ... this product also bought' widget
 */
abstract class ABought extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget target
     */
    const WIDGET_TARGET_PRODUCT = 'product';

    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Get max count of items
     *
     * @return integer
     */
    abstract protected function getMaxCount();

    /**
     * Returns true if block is enabled
     *
     * @return boolean
     */
    abstract protected function isBlockEnabled();

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = static::getWidgetTarget();

        return $result;
    }

    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET_PRODUCT;
    }

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        unset($this->sortByModes[static::SORT_BY_MODE_AMOUNT]);
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

        if (!\XLite\Core\CMSConnector::isCMSStarted()) {
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
            $this->widgetParams[\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT]->setValue($this->getMaxCount());
        }
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();

        $list[self::PARAM_PRODUCT_ID] = $this->getParam(self::PARAM_PRODUCT_ID);

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
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\ObjectId\Product(
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
        return 'XLite\Module\CDev\ProductAdvisor\View\Pager\Customer\ControllerPager';
    }

    /**
     * Get currently viewed product ID
     *
     * @return integer
     */
    protected function getProductId()
    {
        return (int) \XLite\Core\Request::getInstance()->product_id;
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
            && self::WIDGET_TYPE_SIDEBAR === $this->getParam(self::PARAM_WIDGET_TYPE)
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
        return $this->isBlockEnabled() && parent::isVisible();
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
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-bought';
    }
}
