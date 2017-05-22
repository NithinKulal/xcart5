<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model\Repo;

/**
 * The Order model repository extension
 */
abstract class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Return count by status
     *
     * @param string $statusType Status type
     *
     * @return array
     */
    public function countByStatus($statusType)
    {
        $statusType .= 'Status';

        $result = array();
        $data = $this->createPureQueryBuilder('o')
            ->select('COUNT(o.order_id)')
            ->innerJoin('o.' . $statusType, 's')
            ->addSelect('s.id')
            ->groupBy('o.' . $statusType)
            ->getResult();

        foreach ($data as $v) {
            $result[$v['id']] = $v[1];
        }

        return $result;
    }
}