<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Model\Repo;

/**
 * Abstract repository
 */
class ARepo extends \XLite\Model\Repo\ARepo implements \XLite\Base\IDecorator
{
    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getSitemapGenerationIterator($position = 0)
    {
        return $this->defineSitemapGenerationQueryBuilder($position)
            ->setMaxResults(\XLite\Module\CDev\XMLSitemap\Core\EventListener\SitemapGeneration::CHUNK_LENGTH)
            ->iterate();
    }

    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineSitemapGenerationQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position);
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForSitemapGenerationQuery()
    {
        $qb = $this->defineSitemapGenerationQueryBuilder(0)
            ->setMaxResults(1000000000);

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Count items for sitemap generation
     *
     * @return integer
     */
    public function countForSitemapGeneration()
    {
        return (int)$this->defineCountForSitemapGenerationQuery()->getSingleScalarResult();
    }
}