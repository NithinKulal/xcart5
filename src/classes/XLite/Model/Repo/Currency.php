<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Currency repository
 */
class Currency extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code'),
    );


    /**
     * Find all used into orders currency
     *
     * @return array
     */
    public function findAllSortedByName()
    {
        return $this->defineAllSortedByNameQuery()->getResult();
    }

    /**
     * Find all used into orders currency
     *
     * @return array
     */
    public function findUsed()
    {
        return $this->defineFindUsedQuery()->getResult();
    }


    /**
     * Define query for findAllSortedByName() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineAllSortedByNameQuery()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('translations.name');
    }

    /**
     * Define query for findUsed() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindUsedQuery()
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.orders', 'o', 'WITH', 'o.order_id IS NOT NULL');
    }

    // }}}
}
