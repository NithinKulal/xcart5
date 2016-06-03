<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Model\Repo;

/**
 * Category repository
 */
abstract class Category extends \XLite\Model\Repo\Category implements \XLite\Base\IDecorator
{
    /**
     * Count categories as sitemaps links 
     * 
     * @return integer
     */
    public function countCategoriesAsSitemapsLinks()
    {
        return $this->defineCountQuery()->andWhere('c.parent IS NOT NULL')->count();
    }

    /**
     * Find one as sitemap link 
     * 
     * @param integer $position Position
     *  
     * @return \XLite\Model\Category
     */
    public function  findOneAsSitemapLink($position)
    {
        return $this->createPureQueryBuilder()
            ->andWhere('c.parent IS NOT NULL')
            ->setMaxResults(1)
            ->setFirstResult($position)
            ->getSingleResult();
    }
}

