<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Core;

/**
 * Add2CartPopup class
 *
 * @Decorator\Depend("XC\Add2CartPopup")
 */
class Add2CartPopup extends \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup implements \XLite\Base\IDecorator
{
    /**
     * Get products list
     *
     * @param integer $productId  Current product ID
     * @param array   $productIds Product ID which must be excluded from the search results
     * @param integer $maxCount   Maximum number of products
     *
     * @return array
     */
    public function getSourceCustomerBought($productId, $productIds, $maxCount)
    {
        $result = array();

        $profileIds = \XLite\Core\Database::getRepo('XLite\Model\Order')->findUsersBoughtProduct($productId);

        if ($profileIds) {

            $cnd = new \XLite\Core\CommonCell;

            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_PROFILE_ID} = $profileIds;
            if ($productIds) {
                $cnd->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID} = $productIds;
            }
            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_ORDER_BY} = array('cnt', 'DESC');
            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_PA_GROUP_BY} = 'p.product_id';
            $cnd->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_LIMIT} = array(0, $maxCount + 1);

            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, false);

            foreach ($products as $product) {
                if ($product->getProductId() != $productId) {
                    $result[] = $product;
                }
            }
        }

        return $result;
    }

    /**
     * Register products source 'Customers also bought...' for 'Add to Cart popup' module
     *
     * @return array
     */
    protected function getSources()
    {
        $sources = parent::getSources();
        $sources['PAB'] = array(
            'method' => 'getSourceCustomerBought',
            'position' => 200,
        );

        return $sources;
    }
}
