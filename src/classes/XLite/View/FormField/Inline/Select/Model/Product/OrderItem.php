<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select\Model\Product;

/**
 * OrderItem model inline selector
 */
class OrderItem extends \XLite\View\FormField\Inline\Select\Model\Product\ProductSelector
{
    /**
     * Save widget value in entity
     *
     * @param array $field Field data
     *
     * @return void
     */
    public function saveValueName($field)
    {
        $productId = (int) $field['widget']->getValue();
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($productId);

        if ($product) {
            $this->getEntity()->setProduct($product);
            $this->getEntity()->setName($product->getName());
            $this->getEntity()->setAttributeValues($product->prepareAttributeValues());
        }
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\Model\OrderItemSelector';
    }
}
