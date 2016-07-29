<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

/**
 * 
 * 
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
abstract class Page extends \XLite\Module\CDev\SimpleCMS\Model\Repo\Page implements \XLite\Base\IDecorator
{
    const PARAM_ENABLED = 'enabled';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder
            ->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', $value);
    }
}
