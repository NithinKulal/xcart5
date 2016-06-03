<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NextPreviousProduct\View\ItemList\Product\Customer;

use XLite\Module\XC\NextPreviousProduct\View\Product\ListItem;

/**
 * Decorated ACustomer items list
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Item position on page
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Public wrapper for getSearchCondition()
     *
     * @return \XLite\Core\CommonCell
     */
    public function getSearchConditionWrapper()
    {
        return $this->getSearchCondition();
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/NextPreviousProduct/items-list/cookie-setter.js';

        return $list;
    }

    /**
     * Get three items around $itemPosition
     *
     * @param \XLite\Core\CommonCell $cnd          Condition for search
     * @param integer                $itemPosition Item position in condition
     *
     * @return array|integer
     */
    public function getNextPreviousItems($cnd, $itemPosition)
    {
        $cnd->limit = array(
            $itemPosition - 1,
            3,
        );

        return $this->getData($cnd);
    }

    /**
     * Public wrapper for getPager()
     *
     * @return \XLite\View\Pager\APager
     */
    public function getPagerWrapper()
    {
        return $this->getPager();
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
        return parent::getProductWidgetParams($product) + [
            ListItem::PARAM_PAGE_ID          => $this->getPager()->getPageIdWrapper(),
            ListItem::PARAM_POSITION_ON_PAGE => $this->position++,
            ListItem::PARAM_ITEM_LIST_CLASS  => get_class($this),
        ];
    }
}
