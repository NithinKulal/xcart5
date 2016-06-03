<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

/**
 * Class represents an order
 *
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Check - is item product id equal specified product id
     *
     * @param \XLite\Model\OrderItem $item      Item
     * @param integer                $productId Product id
     *
     * @return boolean
     */
    public function isItemProductIdEqual(\XLite\Model\OrderItem $item, $productId)
    {
        return parent::isItemProductIdEqual($item, $productId)
            && (
                !$item->getVariant()
                || $item->getVariant()->getDefaultAmount()
            );
    }


    /**
     * Check - is item variant id equal specified variant id
     *
     * @param \XLite\Model\OrderItem $item      Item
     * @param integer                $variantId Variant id
     *
     * @return boolean
     */
    public function isItemVariantIdEqual(\XLite\Model\OrderItem $item, $variantId)
    {
        return $item->getVariant() && $item->getVariant()->getId() == $variantId;
    }

    /**
     * Find items by variant ID
     *
     * @param integer $variantId Variant ID to use
     *
     * @return array
     */
    public function getItemsByVariantId($variantId)
    {
        $items = $this->getItems();

        return \Includes\Utils\ArrayManager::filter(
            $items,
            array($this, 'isItemVariantIdEqual'),
            $variantId
        );
    }

    /**
     * Increase / decrease item product inventory
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param integer                $sign Flag; "1" or "-1"
     * @param boolean                $register  Register in order history OPTIONAL
     *
     * @return void
     */
    protected function changeItemInventory($item, $sign, $register = true)
    {
        $amount = parent::changeItemInventory($item, $sign, $register);

        if ((bool) $item->getVariant() && $register) {
            $history = \XLite\Core\OrderHistory::getInstance();
            $history->registerChangeVariantAmount($this->getOrderId(), $item->getVariant(), $amount);
        }

        return $amount;
    }

    /**
     * Get grouped data item
     *
     * @param \XLite\Model\OrderItem    $item       Order item
     * @param integer                   $amount     Amount
     *
     * @return array
     */
    protected function getGroupedDataItem($item, $amount)
    {
        $result = parent::getGroupedDataItem($item, $amount);

        if ($item->getVariant()) {
            $result =  array(
                'item'      => $item,
                'amount'    => $item->getVariant()->getPublicAmount(),
                'delta'     => $amount,
            );
        }

        return $result;
    }

    /**
     * Return true if item amount limit is reached
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isItemLimitReached($item)
    {
        $result = false;

        $product = $item->getObject();

        if ($product && $product->mustHaveVariants()) {
            $variant = $item->getVariant();
            $result = $variant && $variant->isOutOfStock();

        } else {
            $result = parent::isItemLimitReached($item);
        }

        return $result;
    }
}
