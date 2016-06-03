<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Core;

/**
 * Class to collect labels for displaying in items list
 */
class Labels extends \XLite\Base\Singleton
{
    /**
     * Runtime labels cache
     *
     * @var array
     */
    protected static $labels = array();

    /**
     * Add label
     *
     * @param \XLite\Model\Product $product Product object
     * @param array                $label   Label
     *
     * @return void
     */
    public static function addLabel(\XLite\Model\Product $product, $label)
    {
        static::$labels[$product->getProductId()] = $label;
    }

    /**
     * Get registered label for product
     *
     * @param \XLite\Model\Product $product Product object
     *
     * @return array
     */
    public static function getLabel(\XLite\Model\Product $product)
    {
        if (!isset(static::$labels[$product->getProductId()])) {
            static::$labels[$product->getProductId()] = $product->getFreeShip()
                ? static::getLabelContent()
                : '';
        }

        return !empty(static::$labels[$product->getProductId()])
            ? static::$labels[$product->getProductId()]
            : array();
    }

    /**
     * Get content of Free shipping label
     *
     * @return array
     */
    protected static function getLabelContent()
    {
        return array(
            'blue free-shipping' => \XLite\Core\Translation::getInstance()->translate('FREE'),
        );
    }
}
