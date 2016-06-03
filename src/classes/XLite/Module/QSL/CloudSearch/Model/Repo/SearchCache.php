<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2015 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
