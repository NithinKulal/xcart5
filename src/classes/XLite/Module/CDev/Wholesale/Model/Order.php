<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Order
 */
class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{

    /**
     * Add item to order
     *
     * @param \XLite\Model\OrderItem $newItem Item to add
     *
     * @return boolean
     */
    public function addItem(\XLite\Model\OrderItem $newItem)
    {
        $result = parent::addItem($newItem);
    
        if ($result && $newItem->isValid()) {

            $minQuantity = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity')
                ->getMinQuantity(
                    $newItem->getProduct(),
                    $this->getProfile() ? $this->getProfile()->getMembership() : null
                );

            if (
                $minQuantity
                && $newItem->getAmount() < $minQuantity->getQuantity()
            ) {
                $newItem->setAmount($minQuantity->getQuantity());
            }
        }

        return $result;
    }
}
