<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Model\Repo;

/**
 * Products repository
 */
abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Count products as sitemaps links
     *
     * @return integer
     */
    public function countProductsAsSitemapsLinks()
    {
        return $this->defineProductsAsSitemapsLinksQuery()->count();
    }

    /**
     * Find one product as sitemap link
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\Product
     */
    public function findOneAsSitemapLink($position)
    {
        return $this->defineProductsAsSitemapsLinksQuery()
            ->setMaxResults(1)
            ->setFirstResult($position)
            ->getSingleResult();
    }

    /**
     * Find one product as sitemap link
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\Product
     */
    public function findAsSitemapLink($position, $count = 1)
    {
        return $this->defineProductsAsSitemapsLinksQuery()
            ->setMaxResults($count)
            ->setFirstResult($position)
            ->getResult();
    }

    /**
     * Define specific query to find out products links for sitemap
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineProductsAsSitemapsLinksQuery()
    {
        return $this->createPureQueryBuilder()
            ->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', 1);
    }
}
