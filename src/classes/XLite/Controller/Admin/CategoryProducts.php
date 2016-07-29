<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Category products controller
 */
class CategoryProducts extends \XLite\Controller\Admin\ProductList
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params[] = 'id';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible()
            ? static::t('Manage category (X)', array('category_name' => $this->getCategoryName()))
            : '';
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory()) {
            $this->addLocationNode(
                'Categories',
                $this->buildURL('categories')
            );

            $categories = $this->getCategory()->getPath();
            array_pop($categories);
            foreach ($categories as $category) {
                $this->addLocationNode(
                    $category->getName(),
                    $this->buildURL('categories', '', ['id' => $category->getCategoryId()])
                );
            }
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No category defined')
            : (($categoryName = $this->getCategoryName())
                ? $categoryName
                : static::t('Manage categories')
            );
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->find($this->getCategoryId())->getName();
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->find($this->getCategoryId());
        }

        return $this->category;
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategoryId();
    }
}
