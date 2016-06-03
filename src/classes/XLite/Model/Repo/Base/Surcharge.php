<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Abstract surcharge repository
 */
abstract class Surcharge extends \XLite\Model\Repo\ARepo
{
    /**
     * Get exists types 
     * 
     * @return array
     */
    public function getExistsTypes()
    {
        $result = array();

        foreach ($this->defineGetExistsTypesQuery()->getResult() as $surcharge) {
            $info = $surcharge->getInfo();
            if ($info) {
                $result[] = array(
                    'name'    => $info->name,
                    'code'    => $surcharge->getCode(),
                    'type'    => $surcharge->getType(),
                    'include' => $surcharge->getInclude(),
                );
            }
        }

        return $result;
    }

    /**
     * Define query builder for getExistsTypes() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetExistsTypesQuery()
    {
        $qb = $this->createQueryBuilder();
        $alias = $this->getMainAlias($qb);

        return $qb->groupBy($alias . '.type, ' . $alias . '.code');
    }

}

