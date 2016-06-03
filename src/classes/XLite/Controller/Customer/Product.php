<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Product
 */
class Product extends \XLite\Controller\Customer\Base\Catalog
{
    /**
     * Product
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->params[] = 'product_id';

        $this->product = null;
    }

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function
     *
     * @return void
     */
    public function handleRequest()
    {
        \XLite\Core\Request::getInstance()->product_id = intval(\XLite\Core\Request::getInstance()->product_id);

        parent::handleRequest();
    }

    /**
     * Process an action 'preview'
     *
     * @return void
     */
    public function doActionPreview()
    {
        // Do nothing here, just display product page
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return false;
    }

    /**
     * Get product category id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        $categoryId = parent::getCategoryId();

        if ($this->getRootCategoryId() == $categoryId && $this->getProduct() && $this->getProduct()->getCategoryId()) {
            $categoryId = $this->getProduct()->getCategoryId();
        }

        return $categoryId;
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription()
    {
        return (parent::getDescription() || !$this->getProduct())
            ? parent::getDescription()
            : $this->getProduct()->getBriefDescription();
    }

    /**
     * Return current (or default) product object
     *
     * @return \XLite\Model\Product
     */
    public function getModelObject()
    {
        return ($this->isVisible())
            ? $this->getProduct()
            : null;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = $this->defineProduct();
        }

        return $this->product;
    }

    /**
     * Defines the maximum width of the images
     *
     * @return integer
     */
    public function getMaxImageWidth()
    {
        return $this->getDefaultMaxImageSize(true);
    }

    /**
     * Check - product has Description tab or not
     *
     * @return boolean
     */
    public function hasDescription()
    {
        return 0 < strlen($this->getProduct()->getDescription())
            || $this->hasAttributes();
    }

    /**
     * Check - product has visible attributes or not
     *
     * @return boolean
     */
    public function hasAttributes()
    {
        return 0 < $this->getProduct()->getWeight()
            || 0 < strlen($this->getProduct()->getSku());
    }

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $classes[] = $this->getProduct() && $this->getCart()->isProductAdded($this->getProduct()->getProductId())
            ? 'added-product'
            : 'non-added-product';

        return $classes;
    }


    /**
     * Define product
     *
     * @return \XLite\Model\Product
     */
    protected function defineProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getProduct() ? $this->getProduct()->getName() : null;
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && $this->getProduct()
            && (
                $this->getProduct()->isVisible()
                || $this->isPreview()
            );
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProduct()
            && (
                $this->getProduct()->isVisible()
                || $this->isPreview()
            );
    }

    /**
     * Check it is preview or not
     *
     * @return boolean
     */
    public function isPreview()
    {
        return 'preview' == \XLite\Core\Request::getInstance()->action
            && $this->getProfile()
            && $this->getProfile()->isAdmin()
            && (
                \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
                || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog')
            );
    }

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        return array_merge(
            parent::defineCommonJSData(),
            array(
                'product_id'    => $this->getProductId(),
                'category_id'   => $this->getCategoryId(),
            )
        );
    }
}
