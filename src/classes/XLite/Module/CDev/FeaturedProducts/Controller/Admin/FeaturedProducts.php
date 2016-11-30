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
class FeaturedProducts extends \XLite\Controller\Admin\AAdmin
{
    /**
     * params
     *
     * @var string
     */
    protected $params = array('target', 'id');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->id
            ? static::t('Manage category (X)', array('category_name' => $this->getCategoryName()))
            : static::t('Front page');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory() && \XLite\Core\Request::getInstance()->id) {
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
        if (!$this->isVisible()) {
            return static::t('No category defined');
        } else {
            return (\XLite\Core\Request::getInstance()->id
                ? $this->getCategoryName()
                : static::t('Front page')
            );
        }
    }

    /**
     * @return \XLite\Model\Category
     */
    protected function getCategory()
    {
        $id = \XLite\Core\Request::getInstance()->id ?: $this->getRootCategoryId();

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        $id = \XLite\Core\Request::getInstance()->id ?: $this->getRootCategoryId();

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id)
            ->getName();
    }

    /**
     * Get featured products list
     *
     * @return array(\XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct) Objects
     */
    public function getFeaturedProductsList()
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct')
            ->getFeaturedProducts($this->id);
    }

    /**
     * doActionAdd
     *
     * @return void
     */
    protected function doActionAdd()
    {
        if (isset(\XLite\Core\Request::getInstance()->select)) {
            $pids = \XLite\Core\Request::getInstance()->select;
            $products = \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->findByIds($pids);

            $this->id = \XLite\Core\Request::getInstance()->id ?: $this->getRootCategoryId();
            $category = \XLite\Core\Database::getRepo('\XLite\Model\Category')->find($this->id);

            $existingLinksIds = array();
            $existingLinks = $this->getFeaturedProductsList();

            if ($existingLinks) {
                foreach ($existingLinks as $k => $v) {
                    $existingLinksIds[] = $v->getProduct()->getProductId();
                }
            }

            if ($products) {
                foreach ($products as $product) {
                    if (in_array($product->getProductId(), $existingLinksIds)) {
                        \XLite\Core\TopMessage::addWarning(
                            'The product SKU#"X" is already set as featured for the category',
                            array('SKU' => $product->getSku())
                        );
                    } else {
                        $fp = new \XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct();
                        $fp->setProduct($product);

                        if ($category) {
                            $fp->setCategory($category);
                        }

                        \XLite\Core\Database::getEM()->persist($fp);
                    }
                }
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $this->setReturnURL($this->buildURL(
            'featured_products',
            '',
            \XLite\Core\Request::getInstance()->id
                ? array('id' => $this->id)
                : array()
        ));
    }
}
