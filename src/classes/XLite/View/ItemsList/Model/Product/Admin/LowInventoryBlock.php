<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Products with low inventory list block (for dashboard page)
 */
class LowInventoryBlock extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    /**
     * Get URL of 'More...' link
     *
     * @return string
     */
    public function getMoreLink()
    {
        return $this->buildURL(
            'product_list',
            'search',
            array(
                \XLite\Model\Repo\Product::P_INVENTORY => \XLite\Model\Repo\Product::INV_LOW,
            )
        );
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
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array();
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return false;
    }

    /**
     * Get title of 'More...' link
     *
     * @return string
     */
    public function getMoreLinkTitle()
    {
        return static::t('View all low inventory products');
    }

    /**
     * Do not need the create button with this list
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Define items list columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $allowedColumns = array(
            'sku',
            'name',
            'qty',
        );

        $columns = parent::defineColumns();

        // Remove redundant columns
        foreach ($columns as $k => $v) {
            $columns[$k][static::COLUMN_SORT] = null;
            if (!in_array($k, $allowedColumns)) {
                unset($columns[$k]);
            }
        }

        return $columns;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            array(
                $entity->getUniqueIdentifierName() => $entity->getUniqueIdentifier(),
                'page'                             => 'inventory',
            )
        );
    }

    /**
     * Hide left actions
     *
     * @return array
     */
    protected function getLeftActions()
    {
        return array();
    }

    /**
     * Hide left actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array();
    }

    /**
     * Hide panel
     *
     * @return null
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * Mark all items as non-removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Get pager class
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\SinglePageWithMorePager';
    }

    protected function getPagerParams()
    {
        $params = parent::getPagerParams();

        $params[\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT] = 5;

        return $params;
    }

    /*
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/product/empty_low_inventory_list.twig';
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
     * Prepare search condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_LOW;
        $result->{\XLite\Model\Repo\Product::P_ORDER_BY} = array('p.amount');

        return $result;
    }
}
