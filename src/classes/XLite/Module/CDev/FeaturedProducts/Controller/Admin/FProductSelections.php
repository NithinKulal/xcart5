<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Controller\Admin;

/**
 * Featured products
 */
class FProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    protected $categoryCache = null;

    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @return array
     */
    public function isExcludedProductId($productId)
    {
        return (bool)\XLite\Core\Database::getRepo('XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct')->findOneBy(array(
            'category' => \XLite\Core\Request::getInstance()->currentCategoryID ?: $this->getCondition('categoryId'),
            'product'  => $productId,
        ));
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($this->getCategoryId() != $this->getRootCategoryId())
            ? ($this->getCategory() ? $this->getCategoryTitle() : parent::getTitle())
            : $this->getFrontPageTitle();
    }

    /**
     * Defines the title if it is front page (no category is provided)
     *
     * @return string
     */
    protected function getFrontPageTitle()
    {
        return static::t('Add featured products for the front page');
    }

    /**
     * Defines the title if the category is provided
     *
     * @return string
     */
    protected function getCategoryTitle()
    {
        return static::t('Add featured products for "X"', array('category' => $this->getCategoryName()));
    }

    /**
     * Returns the category object if the category_id parameter is provided
     *
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        if (is_null($this->categoryCache)) {
            $this->categoryCache = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($this->getCategoryId());
        }
        return $this->categoryCache;
    }

    /**
     * Returns the stylish category path or space if there is no valid category
     *
     * @return string
     */
    protected function getCategoryName()
    {
        return $this->getCategory()
            ? implode(':', \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategoryNamePath($this->getCategoryId()))
            : '';
    }
}
