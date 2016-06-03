<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * The Address model repository
 */
class Address extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Find the list of all cities registered in existing addresses
     *
     * @return array
     */
    public function findAllCities()
    {
        $result = $this->defineFindAllCities()->getResult();

        $cities = array();

        foreach ($result as $res) {
            $cities[] = $res->getCity();
        }

        return $cities;
    }

    /**
     * defineFindAllCities
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindAllCities()
    {
        return $this->createQueryBuilder()
            ->select('a.city')
            ->addGroupBy('a.city')
            ->addOrderBy('a.city');
    }

}
