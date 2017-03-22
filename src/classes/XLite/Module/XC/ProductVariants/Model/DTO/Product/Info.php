<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\DTO\Product;


class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected static function isSKUValid($dto)
    {
        if (parent::isSKUValid($dto)) {
            $sku = $dto->default->sku;
            return !\XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->findOneBySku($sku);
        }

        return false;
    }
}