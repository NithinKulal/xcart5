<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

/**
 * The "product" repo class
 */
class SearchCache extends \XLite\Model\Repo\ARepo
{
    /**
     * One hour of search actuality
     */
    const TTL = 3600;
    
    public function getCachedSearch($query)
    {
        $this->clearGarbage();

        $result = $this->createQueryBuilder('s')
            ->select('s')
            ->andWhere('s.keyValue = :key')
            ->andWhere('s.actualTime > :now')
            ->setParameter('key', $this->getKey($query))
            ->setParameter('now', LC_START_TIME)
            ->getSingleResult();

        return $result ? $result->getResultValue() : null;
    }

    public function storeCachedSearch($query, $result)
    {
        $searchCache = new \XLite\Module\QSL\CloudSearch\Model\SearchCache();

        $searchCache->setKeyValue($this->getKey($query));
        $searchCache->setResultValue($result);
        $searchCache->setActualTime(LC_START_TIME + static::TTL);

        \XLite\Core\Database::getEM()->persist($searchCache);
        \XLite\Core\Database::getEM()->flush($searchCache);
    }

    protected function clearGarbage()
    {
        $this->getQueryBuilder()
            ->delete($this->_entityName, 's')
            ->andWhere('s.actualTime < :now')
            ->setParameter('now', LC_START_TIME)
            ->execute();
    }
    
    protected function getKey($query)
    {
        return hash('md4', serialize($query));
    }
}
