<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Money modificators repository
 */
class MoneyModificator extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('class'),
    );


    /**
     * Find active modificators list
     * 
     * @return array
     */
    public function findActive()
    {
        return $this->defineFindActiveQuery()->getResult();
    }

    /**
     * Define query for findActive() method
     * 
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindActiveQuery()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.position', 'asc');
    }
}

