<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Category products
 */
class CategoryProducts extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    const SORT_BY_MODE_POSITION = 'cp.orderby';

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('category_products'));
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return null;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'category_products';
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'id' => $this->getCategoryId(),
            )
        );
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL(
            'product',
            '',
            array(
                'category_id' => $this->getCategoryId(),
            )
        );
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Get search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{static::PARAM_CATEGORY_ID} = $this->getCategoryId();

        return $result;
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return true;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $list = parent::defineColumns();
        unset($list['category']);
        foreach ($list as $name => $info) {
            unset($list[$name][static::COLUMN_SORT]);
        }
        return $list;
    }

    /**
     * Defines the position MOVE widget class name
     *
     * @return string
     */
    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\CategoryProducts\Move';
    }

    /**
     * Defines the position OrderBy widget class name
     *
     * @return string
     */
    protected function getOrderByWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\CategoryProducts\OrderBy';
    }

    /**
     * Defines the position of product in the current category
     *
     * @param \XLite\Model\Product $product
     *
     * @return integer
     */
    protected function getPositionColumnValue(\XLite\Model\Product $product)
    {
        return $product->getPosition($this->getCategoryId());
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id'] = $this->getCategoryId();

        return $this->commonParams;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.product.blank');
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Product\Admin\CategoryProducts';
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_POSITION;
    }
}
