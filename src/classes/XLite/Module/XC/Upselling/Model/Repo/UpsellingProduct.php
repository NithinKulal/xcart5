<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Model\Repo;

/**
 * Upselling Product repository
 */
class UpsellingProduct extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    const SEARCH_PARENT_PRODUCT_ID = 'parentProductId';
    const SEARCH_EXCL_PRODUCT_ID   = 'excludingProductId';
    const SEARCH_DATE              = 'date';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderBy';


    /**
     * Get upselling products list
     *
     * @param integer $productId Product ID
     *
     * @return array(\XLite\Module\XC\Upselling\Model\UpsellingProduct) Objects
     */
    public function getUpsellingProducts($productId)
    {
        return $this->findByParentProductId($productId);
    }

    /**
     * Find by type
     *
     * @param integer $productId Product ID
     *
     * @return array
     */
    protected function findByParentProductId($productId)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_PARENT_PRODUCT_ID} = $productId;

        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param string                     $value Condition data
     *
     * @return void
     */
    protected function prepareCndParentProductId(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $f = $this->getMainAlias($qb);
        $qb = $qb->innerJoin($f . '.product', 'p')
            ->andWhere($f . '.parentProduct = :parentProductId')
            ->setParameter('parentProductId', $value);

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->assignExternalEnabledCondition($qb, 'p');
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndExcludingProductId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && 1 < count($value)) {
            $queryBuilder->andWhere('p.product_id NOT IN (' . implode(',', $value) . ')');

        } else {
            $queryBuilder->andWhere('p.product_id != :productId')
                ->setParameter('productId', is_array($value) ? array_pop($value) : $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('p.arrivalDate < :date')
            ->setParameter('date', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        list($sort, ) = $this->getSortOrderValue($value);

        if ('translations.name' === $sort) {
            $queryBuilder
                ->linkInner('u.product', 'product')
                ->linkInner('product.translations', 'translations');
        }

        parent::prepareCndOrderBy($queryBuilder, $value);
    }

    // }}}

    /**
     * Add the association link for the upsell product
     *
     * @param \XLite\Module\XC\Upselling\Model\UpsellingProduct $link Related product link
     *
     * @return void
     */
    public function addBidirectionalLink($link)
    {
        $this->changeBidirectionalLink($link, true);
    }

    /**
     * Delete the association link for the upsell product
     *
     * @param \XLite\Module\XC\Upselling\Model\UpsellingProduct $link Related product link
     *
     * @return void
     */
    public function deleteBidirectionalLink($link)
    {
        $this->changeBidirectionalLink($link, false);
    }

    /**
     * Change the association link for the upsell product
     * This routine is used only inside the model
     *
     * @param \XLite\Module\XC\Upselling\Model\UpsellingProduct $link             Related product link
     * @param boolean                                           $newBidirectional Bi-directional flag
     *
     * @return void
     */
    protected function changeBidirectionalLink($link, $newBidirectional)
    {
        $data = array(
            'parentProduct' => $link->getProduct(),
            'product'       => $link->getParentProduct(),
        );
        $aLink = $this->findOneBy($data);
        $aLink ? \XLite\Core\Database::getEM()->remove($aLink) : null;

        if ($newBidirectional) {
            // Need to add link
            $this->insert($data);
        }
    }
}
