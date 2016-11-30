<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Controller\Admin;

/**
 * Upselling products
 */
class UProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID
     *
     * @return array
     */
    public function isExcludedProductId($productId)
    {
        $upsellingProduct = array(
            'parentProduct' => \XLite\Core\Request::getInstance()->product_id,
            'product'       => $productId,
        );

        return \XLite\Core\Request::getInstance()->product_id == $productId
            || (bool)\XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
                ->findOneBy($upsellingProduct);
    }

    /**
     * Specific title for the excluded product
     * By default it is 'Already added'
     *
     * @param integer $productId Product ID
     *
     * @return string
     */
    public function getTitleExcludedProduct($productId)
    {
        return \XLite\Core\Request::getInstance()->product_id == $productId
            ? static::t('You cannot choose this product')
            : parent::getTitleExcludedProduct($productId);
    }

    /**
     * Specific CSS class for the image of the excluded product.
     * You can check the Font Awesome CSS library if you want some specific icons
     *
     * @param integer $productId Product ID
     *
     * @return string
     */
    public function getStyleExcludedProduct($productId)
    {
        return \XLite\Core\Request::getInstance()->product_id == $productId
            ? 'fa-ban'
            : parent::getStyleExcludedProduct($productId);
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return \XLite\Core\Request::getInstance()->itemsList
            ?: '\XLite\Module\XC\Upselling\View\ItemsList\Model\ProductSelection';
    }
}
