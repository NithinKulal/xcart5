<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Repo;

/**
 * Order  repository
 */
abstract class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Find all orders bu profile WithEgoods
     *
     * @param \XLite\Model\Profile $profile NOT OPTIONAL (default value is deprecated)
     *
     * @return void
     */
    public function findAllOrdersWithEgoods(\XLite\Model\Profile $profile = null)
    {
        $list = array();

        if ($profile) {
            foreach ($this->defineFindAllOrdersWithEgoodsQuery($profile)->getResult() as $order) {
                if ($order->getDownloadAttachments()) {
                    $list[] = $order;
                }
            }
        }

        return $list;
    }

    /**
     * Define query for findAllOrdersWithEgoods() method
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return \XLite\Model\QuieryBuilder\AQueryBuilder
     */
    protected function defineFindAllOrdersWithEgoodsQuery(\XLite\Model\Profile $profile = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.items', 'item')
            ->innerJoin('item.privateAttachments', 'pa')
            ->leftJoin('o.orig_profile', 'op')
            ->orderBy('o.date', 'desc');

        if ($profile) {
            $qb->andWhere('op.profile_id = :opid')
                ->setParameter('opid', $profile->getProfileId());
        }

        return $qb;

    }
}

