<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Logic\ImageResize;

/**
 * ImageResize
 */
class Generator extends \XLite\Logic\ImageResize\Generator implements \XLite\Base\IDecorator
{
    const MODEL_PRODUCT_VARIANT = 'XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image';

    /**
     * Returns dimensions for given class
     *
     * @param string $class Class
     *
     * @return array
     */
    public static function getModelImageSizes($class)
    {
        if (static::MODEL_PRODUCT_VARIANT === $class) {
            $class = static::MODEL_PRODUCT;
        }

        return parent::getModelImageSizes($class);
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $list = parent::defineSteps();
        $list[] = 'XLite\Module\XC\ProductVariants\Logic\ImageResize\Step\ProductVariants';

        return $list;
    }
}