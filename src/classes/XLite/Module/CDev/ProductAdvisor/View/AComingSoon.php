<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

use XLite\View\CacheableTrait;

/**
 * Coming soon abstract widget class
 *
 */
abstract class AComingSoon extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget parameter names
     */
    const PARAM_ROOT_ID     = 'rootId';
    const PARAM_USE_NODE    = 'useNode';
    const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Widget target
     */
    const WIDGET_TARGET_COMING_SOON = 'coming_soon';


    /**
     * Return target to retrieve this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return self::WIDGET_TARGET_COMING_SOON;
    }


    /**
     * We remove the sort by modes selector from the coming soon widgets
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->sortByModes = array();
    }


    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' coming-soon-products';
    }


    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Coming soon');
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

        $currentDate = \XLite\Core\Converter::getDayEnd(\XLite\Base\SuperClass::getUserTime());
        $searchCase->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_ARRIVAL_DATE} = array(
            $currentDate,
            null
        );

        return $searchCase;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array|null
     */
    protected function getOrderBy()
    {
        return [static::SORT_BY_MODE_DATE, static::SORT_ORDER_DESC];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_enabled
            && parent::isVisible()
            && $this->hasResults();
    }

    /**
     * Set default sort mode by descending of products arrival date
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DATE;
    }

    /**
     * Get max number of products displayed in block
     *
     * @return integer
     */
    protected function getMaxCountInBlock()
    {
        return (int) \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_max_count_in_block ?: 3;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-coming-soon';
    }

    /**
     * Return false as 'Add to cart' buttons should not be displayed for 'Coming soon' products
     *
     * @param \XLite\Model\Product $product Product object OPTIONAL
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton($product = null)
    {
        return false;
    }
}
