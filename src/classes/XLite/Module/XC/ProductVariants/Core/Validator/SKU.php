<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\Validator;

/**
 * Product SKU
 */
class SKU extends \XLite\Core\Validator\SKU implements \XLite\Base\IDecorator
{
    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function validate($data)
    {
        parent::validate($data);

        if (!\XLite\Core\Converter::isEmptyString($data)) {

            $entity = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')
                ->findOneBySku($this->sanitize($data));

            if ($entity) {
                $this->throwVariantSKUError();
            }
        }
    }

    /**
     * Specific throwError
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwVariantSKUError()
    {
        throw $this->throwError('SKU is not unique (has duplicate assigned to product variant)');
    }
}
