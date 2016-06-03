<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\Model\Repo;

/**
 * Category repository
 */
abstract class Category extends \XLite\Model\Repo\Category implements \XLite\Base\IDecorator
{
    /**
     * Check if display number of prducts
     *
     * @return boolean
     */
    protected function isShowProductNum()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num;
    }

    /**
     * Get categories as dtos queryBuilder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function getCategoriesAsDTOQueryBuilder()
    {
        $queryBuilder = parent::getCategoriesAsDTOQueryBuilder();

        if ($this->isShowProductNum()) {
            $queryBuilder->addSelect('COUNT(subcategoriesProducts) as productsCount');

            $queryBuilder->leftJoin(
                '\XLite\Model\Category',
                'subcategory',
                'WITH',
                'subcategory.lpos > c.lpos AND subcategory.lpos < c.rpos OR subcategory.category_id = c.category_id'
            );
            $queryBuilder->leftJoin(
                'subcategory.categoryProducts',
                'subcategoriesProducts'
            );
            // Enabled condition
            $queryBuilder->leftJoin(
                'subcategoriesProducts.product',
                'subcategoriesProductsProduct'
            );
            $queryBuilder->andWhere('subcategoriesProductsProduct.enabled = :enabled');
            $queryBuilder->setParameter('enabled', true);

            $this->addProductMembershipCondition($queryBuilder, 'subcategoriesProductsProduct');
        }

        return $queryBuilder;
    }

    /**
     * Adds additional condition to the query for checking if product is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addProductMembershipCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getMembershipCondition()) {
            $alias = $alias ?: $this->getDefaultAlias();
            $membership = \XLite\Core\Auth::getInstance()->getMembershipId();

            if ($membership) {
                $queryBuilder->leftJoin($alias . '.memberships', 'productMembership')
                    ->andWhere('productMembership.membership_id = :membershipId OR productMembership.membership_id IS NULL')
                    ->setParameter('membershipId', \XLite\Core\Auth::getInstance()->getMembershipId());

            } else {
                $queryBuilder->leftJoin($alias . '.memberships', 'productMembership')
                    ->andWhere('productMembership.membership_id IS NULL');
            }
        }
    }
}

