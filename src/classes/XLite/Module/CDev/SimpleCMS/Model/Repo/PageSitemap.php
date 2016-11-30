<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Model\Repo;

/**
 * Page repo
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class PageSitemap extends \XLite\Module\CDev\SimpleCMS\Model\Repo\Page implements \XLite\Base\IDecorator
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineSitemapGenerationQueryBuilder($position)
    {
        $qb = parent::defineSitemapGenerationQueryBuilder($position);

        $qb->select($qb->getMainAlias() . '.id')
            ->andWhere($qb->getMainAlias() . '.enabled = true');

        $this->addCleanURLCondition($qb);

        return $qb;
    }

    /**
     * Add clean url if applicable
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function addCleanURLCondition(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        if (\XLite\Module\CDev\SimpleCMS\Logic\Sitemap\Step\Page::isSitemapCleanUrlConditionApplicable()) {
            $qb->addSelect('cu.cleanURL')
                ->leftJoin('XLite\Model\CleanURL', 'cu', \Doctrine\ORM\Query\Expr\Join::WITH, 'cu.page = '.$qb->getMainAlias());
        }

        return $qb;
    }
}