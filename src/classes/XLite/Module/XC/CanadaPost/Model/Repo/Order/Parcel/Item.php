<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Repo\Order\Parcel;

/**
 * Class represents a Canada Post parcel items repository
 */
class Item extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_PARCEL_ID = 'parcelId';

    /**
     * Move item from a parcel to an another parcel or a new one
     *
     * @param integer        $itemId   Item ID
     * @param integer        $amount   Amount
     * @param integet|string $parcelId Parcel ID or "NEW" string for new parcels
     *
     * @return void
     */ 
    public function moveItem($itemId, $amount, $parcelId)
    {
        $amount = intval($amount);

        // Get parcel item model
        $item = $this->find($itemId);

        if (!$item->getParcel()->isEditable()) {
            // item cannt be moved - parcel is not editable
            $item = null;
        }

        if (isset($item)) {

            $amount = min($amount, $item->getAmount());

            // Get parcel model or create new one
            if ($parcelId == 'NEW') {

                $parcel = $item->getParcel()->cloneEntity(false);

                $parcel->setNumber($parcel->getOrder()->countCapostParcels() + 1);
                $parcel->create();

            } else {

                $parcel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CanadaPost\Model\Order\Parcel')->find($parcelId);
            }

            if (!$parcel->isEditable()) {
                // item cannt be moved to here - parcel is not editable
                $parcel = null;
            }

            if (isset($parcel)) {

                // Parcel successfully created or found, so we can move the item

                $newItem = null;

                if ($parcel->hasItems()) {

                    // Try to find the same item
                    foreach ($parcel->getItems() as $_item) {

                        if ($item->getOrderItem()->getItemId() == $_item->getOrderItem()->getItemId()) {
                            $newItem = $_item;
                            break;
                        }
                    }
                }

                if (!isset($newItem)) {

                    // Create new item object
                    $newItem = new \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Item();
                    \XLite\Core\Database::getEM()->persist($newItem);

                    $newItem->setParcel($parcel);
                    $newItem->setOrderItem($item->getOrderItem());

                    $newItem->setWeight($item->getWeight());
                }

                $newItem->setAmount($newItem->getAmount() + $amount);

                $item->setAmount($item->getAmount() - $amount);

                if ($item->getAmount() <= 0) {
                    // Remove item
                    $item->delete();
                }

                \XLite\Core\Database::getEM()->flush();
            }
        }
    }

    // }}}

    // {{{ Search: prepare conditions

    /**
     * Prepare "parcel ID" condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param integer                    $value Parcel ID
     *
     * @return void
     */
    protected function prepareCndParcelId(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        if (!empty($value)) {
            $qb->linkInner('i.parcel', 'p');
            $qb->andWhere('p.id = :parcelId')
                ->setParameter('parcelId', $value);
        }
    }

    // }}}
}
