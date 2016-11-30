<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\ItemsList\Model;

/**
 * F products items list
 */
class FeaturedProduct extends \XLite\View\ItemsList\Model\Table
{
    const PARAM_CATEGORY_ID = 'id';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/FeaturedProducts/f_products/style.css';

        return $list;
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions   = parent::getTopActions();
        $actions[] = 'modules/CDev/FeaturedProducts/f_products/parts/create.twig';

        return $actions;
    }

    /**
     * Define the URL for popup product selector
     *
     * @return string
     */
    protected function getRedirectURL()
    {
        return $this->buildURL(
            'featured_products',
            'add',
            \XLite\Core\Request::getInstance()->id
                ? [
                    'id' => \XLite\Core\Request::getInstance()->id,
                ]
                : []
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku'     => [
                static::COLUMN_NAME    => static::t('SKU'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'product' => [
                static::COLUMN_NAME     => static::t('Product'),
                static::COLUMN_TEMPLATE => 'modules/CDev/FeaturedProducts/f_products/parts/info.product.twig',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_ORDERBY  => 200,
            ],
            'price'   => [
                static::COLUMN_NAME     => static::t('Price'),
                static::COLUMN_TEMPLATE => 'modules/CDev/FeaturedProducts/f_products/parts/info.price.twig',
                static::COLUMN_ORDERBY  => 300,
            ],
            'amount'  => [
                static::COLUMN_NAME    => static::t('Stock'),
                static::COLUMN_ORDERBY => 400,
            ],
        ];
    }

    /**
     * The product column displays the product name
     *
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected function preprocessProduct(\XLite\Model\Product $product)
    {
        return $product->getName();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
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

    // }}}

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\TypeInt(
                'CategoryID ', $this->getCategoryId(), false
            ),
        ];
    }

    /**
     * The category ID is defined from the 'id' request variable
     * or the root category id
     *
     * @return string
     */
    protected function getCategoryId()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        return $id ?: $this->getRootCategoryId();
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' f_products';
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return true;
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\FeaturedProducts\View\StickyPanel\ItemsList\FeaturedProduct';
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Module\CDev\FeaturedProducts\Model\Repo\FeaturedProduct::SEARCH_CATEGORY_ID => static::PARAM_CATEGORY_ID,
        ];
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams                            = parent::getCommonParams();
        $this->commonParams[static::PARAM_CATEGORY_ID] = \XLite\Core\Request::getInstance()->{static::PARAM_CATEGORY_ID};

        return $this->commonParams;
    }

    // }}}
}
