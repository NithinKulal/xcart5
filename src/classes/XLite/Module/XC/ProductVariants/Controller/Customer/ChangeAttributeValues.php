<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Controller\Customer;

/**
 * Change attribute values from cart / wishlist item
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues implements \XLite\Base\IDecorator
{
    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;

    /**
     * Change product attribute values
     *
     * @param array $attributeValues Attrbiute values (prepared, from request)
     *
     * @return boolean
     */
    protected function saveAttributeValues(array $attributeValues)
    {
        $result = true;

        if ($this->getItem()->getProduct()->mustHaveVariants()) {
            $variant = $this->getItem()->getProduct()->getVariantByAttributeValues($attributeValues);

            if ($variant && 0 < $variant->getAvailableAmount()) {
                $this->getItem()->setVariant($variant);

            } else {
                $result = false;
                $this->errorMessage = static::t(
                    'Product with selected attribute value(s) is not available or out of stock. Please select other.'
                );

            }
        }

        return $result && parent::saveAttributeValues($attributeValues);
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return $this->errorMessage ?: parent::getErrorMessage();
    }
}
