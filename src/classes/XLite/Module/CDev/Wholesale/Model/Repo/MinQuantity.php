<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model\Repo;

/**
 * MinQuantity model repository
 */
class MinQuantity extends \XLite\Model\Repo\ARepo
{
    /**
     * Get minimum quantities for every membeship
     *
     * @param \XLite\Model\Product $product Product entity
     *
     * @return array
     */
    public function getAllMinQuantities(\XLite\Model\Product $product)
    {
        $result = [];

        $data = $this->getMinQuantity($product);

        $result[] = [
            'name'         => 'All customers',
            'membershipId' => 0,
            'quantity'     => $data ? $data->getQuantity() : 1,
        ];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findAll() as $membership) {

            $data = $this->getMinQuantity($product, $membership);

            $result[] = [
                'name'         => $membership->getName(),
                'membershipId' => $membership->getMembershipId(),
                'quantity'     => $data ? $data->getQuantity() : 1,
            ];
        }

        return $result;
    }

    /**
     * Remove minimum quantity information for a given product.
     *
     * @param \XLite\Model\Product $product Product object to remove
     *
     * @return void
     */
    public function deleteByProduct(\XLite\Model\Product $product)
    {
        $this->defineDeleteByProductQuery($product)->execute();

        $this->flushChanges();
    }

    /**
     * Remove minimum quantity information for a given product.
     *
     * @param \XLite\Model\Product $product Product object to remove
     * @param array                $memberships
     */
    public function deleteByProductAndMemberships(\XLite\Model\Product $product, array $memberships = [])
    {
        if (count($memberships)) {
            $this->defineDeleteByProductAndMembershipsQuery($product, $memberships)->execute();
            $this->flushChanges();
        }
    }

    /**
     * Get minimum quantities for specified product and membership
     *
     * @param \XLite\Model\Product    $product    Product entity
     * @param \XLite\Model\Membership $membership Membership entity (or null) OPTIONAL
     *
     * @return \XLite\Module\CDev\Wholesale\Model\MinQuantity
     */
    public function getMinQuantity(\XLite\Model\Product $product, $membership = null)
    {
        return $this->defineMinQuantitiesQuery($product, $membership)->setMaxResults(1)->getSingleResult();
    }

    /**
     * Define query builder for getMinQuantities()
     *
     * @param \XLite\Model\Product    $product    Product entity
     * @param \XLite\Model\Membership $membership Membership entity (or null) OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineMinQuantitiesQuery($product, $membership = null)
    {
        $qb = $this->createQueryBuilder('m');

        $qb->innerJoin('m.product', 'product')
            ->andWhere('product.product_id = :productId')
            ->setParameter('productId', $product->getProductId());

        if (!is_null($membership)) {
            $qb->innerJoin('m.membership', 'membership')
                ->andWhere('membership.membership_id = :membershipId')
                ->addOrderBy('membership.membership_id')
                ->setParameter('membershipId', $membership->getMembershipId());
        } else {

            $qb->andWhere('m.membership is null');
        }

        return $qb;
    }

    /**
     * Define query builder for deleteByProduct()
     *
     * @param \XLite\Model\Product $product Product entity
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineDeleteByProductQuery(\XLite\Model\Product $product)
    {
        $qb = $this->getQueryBuilder()->delete($this->_entityName, 'm');

        $this->prepareCndProduct($qb, $product);

        return $qb;
    }

    /**
     * Define query builder for deleteByProduct()
     *
     * @param \XLite\Model\Product $product Product entity
     * @param array                $memberships
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineDeleteByProductAndMembershipsQuery(\XLite\Model\Product $product, $memberships)
    {
        $qb = $this->getQueryBuilder()->delete($this->_entityName, 'm');

        $this->prepareCndProduct($qb, $product);

        $membershipExpr = $qb->expr()->in('m.membership', $memberships);

        if (in_array('NULL', $memberships, true)) {
            $membershipExpr = $qb->expr()->orX(
                $membershipExpr,
                $qb->expr()->isNull('m.membership')
            );
        }

        $qb->andWhere($membershipExpr);

        return $qb;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb      Query builder to prepare
     * @param \XLite\Model\Product       $product Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $qb, \XLite\Model\Product $product)
    {
        $qb->andWhere('m.product = :product')
            ->setParameter('product', $product);
    }
}
