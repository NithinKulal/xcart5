<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Attribute;
use XLite\Module\XC\ProductVariants\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Product;
use XLite\Module\XC\ProductVariants\Model\ProductVariant;

/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"XC\ProductVariants"})
 */
abstract class StoreApiProductVariants extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get product variants data.
     *
     * @param Product $product
     * @param         $attributes
     *
     * @return array
     */
    protected function getProductVariants(Product $product, $attributes)
    {
        $variants = [];

        if ($product->getVariants()->count() > 0) {
            $variantAttrIds = array_map(function ($v) {
                return $v->getAttribute()->getId();
            }, $product->getVariants()->first()->getValues());

            $commonAttrs = array_filter($attributes, function ($attr) use ($variantAttrIds) {
                return !in_array($attr['id'], $variantAttrIds);
            });

            /** @var ProductVariant $variant */
            foreach ($product->getVariants() as $variant) {
                if ($variant->isOutOfStock()) {
                    continue;
                }

                $variantData = [
                    'id'         => $variant->getId(),
                    'price'      => $variant->getDisplayPrice(),
                    'attributes' => [],
                ];

                /** @var AttributeValueSelect $value */
                foreach ($variant->getValues() as $value) {
                    /** @var Attribute $attribute */
                    $attribute = $value->getAttribute();

                    $variantData['attributes'][] = array(
                        'id'     => $attribute->getId(),
                        'name'   => htmlspecialchars_decode($attribute->getName()),
                        'values' => [$value->asString()],
                    );
                }

                $variantData['attributes'] = array_merge($variantData['attributes'], $commonAttrs);

                $variants[] = $variantData;
            }

            if (empty($variants)) {
                // Return a fake variant if all existing variants are out of stock to allow filtering on regular (non-variant) attributes. This keeps behavior consistent with regular products.

                return parent::getProductVariants($product, $commonAttrs);
            }

        } else {
            return parent::getProductVariants($product, $attributes);
        }

        return $variants;
    }

    /**
     * Get product SKUs (multiple if there are variants)
     *
     * @param $product
     *
     * @return array
     */
    protected function getSkus($product)
    {
        $skus = parent::getSkus($product);

        foreach ($product->getVariants() as $variant) {
            $skus[] = $variant->getSku();
        }

        return $skus;
    }
}
