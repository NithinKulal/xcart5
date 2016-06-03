<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Order tracking number repository
 */
class OrderTrackingNumber extends \XLite\Model\Repo\ARepo
{
    /**
     * Search parameter names
     */
    const P_ORDER_ID = 'orderId';

    /**
     * Get default alias
     *
     * @return string
     */
    public function getDefaultAlias()
    {
        return 'tr';
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('tr.order = :order')
                ->setParameter('order', $value);
        }
    }
}
