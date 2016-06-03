<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Model\Repo;

/**
 * The Order model repository
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Find customers who ordered product with specified product ID 
     * Returns array of profile IDs
     * 
     * @param integer $productId Product ID
     *  
     * @return array
     */
    public function findUsersBoughtProduct($productId)
    {
        $result = array();
    
        $data = $this->defineFindUsersBoughtProductQuery($productId)->getResult();

        if ($data) {
            foreach ($data as $row) {
                $result[] = intval($row['profile_id']);
            }
            $result = array_unique($result);
        }

        return $result;
    }

    /**
     * Prepare query builder
     *
     * @param array $productId Product ID
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindUsersBoughtProductQuery($productId)
    {
        return $this->createQueryBuilder('o')
            ->select('profile.profile_id')
            ->innerJoin('o.items', 'oi')
            ->innerJoin('oi.object', 'product', 'WITH', 'product.product_id = :productId')
            ->innerJoin('o.orig_profile', 'profile')
            ->setParameter('productId', $productId);
    }
}
