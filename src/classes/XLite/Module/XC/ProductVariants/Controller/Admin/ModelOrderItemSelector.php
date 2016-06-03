<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Controller\Admin;

/**
 * OrderItem model selector controller
 */
class ModelOrderItemSelector extends \XLite\Controller\Admin\ModelOrderItemSelector implements \XLite\Base\IDecorator
{
    /**
     * Add selected variant to the order item
     *
     * @param \XLite\Model\OrderItem $orderItem Order item entity
     *
     * @return \XLite\Model\OrderItem
     */
    protected function postprocessOrderItem(\XLite\Model\OrderItem $orderItem)
    {
        $orderItem = parent::postprocessOrderItem($orderItem);

        if ($orderItem->getProduct()->mustHaveVariants()) {
            $variant = $orderItem->getProduct()->getVariantByAttributeValuesIds(
                $orderItem->getAttributeValuesIds()
            );

            if ($variant) {
                $orderItem->setVariant($variant);
                $orderItem->setSku($variant->getDisplaySku());
            }
        }

        return $orderItem;
    }
}
