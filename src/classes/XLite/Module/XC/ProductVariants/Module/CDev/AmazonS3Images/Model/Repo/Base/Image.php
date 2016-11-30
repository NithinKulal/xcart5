<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\CDev\AmazonS3Images\Model\Repo\Base;

/**
 * Base image model extension
 *
 * @Decorator\Depend ("CDev\AmazonS3Images")
 */
abstract class Image extends \XLite\Model\Repo\Base\Image implements \XLite\Base\IDecorator
{
    /**
     * Get managed image repositories
     *
     * @return array
     */
    public static function getManagedRepositories()
    {
        $result = parent::getManagedRepositories();
        $result[] = 'XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image';

        return $result;
    }
}
