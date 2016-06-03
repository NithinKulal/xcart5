<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Controller\Admin;

/**
 * Product variant
 */
class ProductVariant extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Backward compatibility
     *
     * @var array
     */
    protected $params = array('target', 'id', 'page', 'backURL');

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->getProductVariant()
            && $this->getPages();
    }

    /**
     * Return product variant
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    public function getProductVariant()
    {
        if (is_null($this->productVariant)) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant');
            $this->productVariant = $repo->find((int) \XLite\Core\Request::getInstance()->id);
        }

        return $this->productVariant;
    }

    /**
     * Return product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getProduct()
            : null;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $result = $this->getProduct()
            ? $this->getProduct()->getName()
            : '';

        $pages = $this->getPages();
        if ($result
            && $pages
            && 1 === count($pages)
        ) {
            $result .= ' - ' . array_shift($pages);
        }

        return $result;
    }
}
