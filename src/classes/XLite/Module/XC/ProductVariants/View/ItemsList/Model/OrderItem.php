<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\ItemsList\Model;

/**
 * Order items list
 */
class OrderItem extends \XLite\View\ItemsList\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Do some actions before save order items
     *
     * @param boolean                $isUpdated True if action item is updated
     * @param \XLite\Model\OrderItem $entity    OrderItem entity
     *
     * @return void
     */
    protected function postprocessOrderItems($isUpdated = false, $entity = null)
    {
        foreach ($this->getOrder()->getItems() as $item) {

            if ($item->getProduct()->mustHaveVariants()) {
                $variant = $item->getProduct()->getVariantByAttributeValuesIds(
                    $item->getAttributeValuesIds()
                );

                if ($variant) {
                    $item->setVariant($variant);
                    $item->setSku($variant->getDisplaySku());
                }
            }
        }

        parent::postprocessOrderItems($isUpdated, $entity);
    }

    /**
     * Change product quantity in stock if needed
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return void
     */
    protected function changeItemAmountInStock($entity)
    {
        if ($entity->getVariant()) {

            $oldVariant = $this->orderItemsData[$entity->getItemId()]['variant'];
            $newVariant = $entity->getVariant();

            if ($this->isItemDataChangedVariant($oldVariant, $newVariant)) {

                // Return old variant amount to stock
                if (!$oldVariant->getDefaultAmount()) {
                    $oldVariant->changeAmount($this->orderItemsData[$entity->getItemId()]['amount']);

                } else {
                    $entity->getProduct()->changeAmount($this->orderItemsData[$entity->getItemId()]['amount']);
                }

                // Get new variant amount from stock
                if (!$newVariant->getDefaultAmount()) {
                    $newVariant->changeAmount(-1 * $entity->getAmount());

                } else {
                    $entity->getProduct()->changeAmount(-1 * $entity->getAmount());
                }

            } else {
                parent::changeItemAmountInStock($entity);
            }

        } else {
            parent::changeItemAmountInStock($entity);
        }
    }

    /**
     * Add 'variant' to the order items data fields list
     *
     * @return array
     */
    protected function getOrderItemsDataFields()
    {
        $result = parent::getOrderItemsDataFields();
        $result[] = 'variant';

        return $result;
    }

    /**
     * Check order item and return true if this is valid
     *
     * @param \XLite\Model\OrderItem $entity Order item entity
     *
     * @return boolean
     */
    protected function isValidEntity($entity)
    {
        $result = parent::isValidEntity($entity);

        if (
            $result
            && (
                $entity->getProduct()->mustHaveVariants()
                || $entity->getVariant()
            )
        ) {
            $variant = $entity->getProduct()->getVariantByAttributeValuesIds($entity->getAttributeValuesIds());
            $result = $variant
                && $entity->getVariant()
                && $variant->getId() == $entity->getVariant()->getId();
        }

        return $result;
    }

    /**
     * Return true is variants are different
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $old Old product variant
     * param \XLite\Module\XC\ProductVariants\Model\ProductVariant $new New product variant
     *
     * @return boolean
     */
    protected function isItemDataChangedVariant($old, $new)
    {
        return $old && $new && $old->getId() != $new->getId();
    }
}
