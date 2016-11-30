<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core;

/**
 * Database
 */
abstract class Database extends \XLite\Core\Database implements \XLite\Base\IDecorator
{

    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        parent::startEntityManager();

        if (!defined('LC_CACHE_BUILDING')) {
            if (\XLite\Module\XC\WebmasterKit\Core\Profiler::getInstance()->enabled) {
                static::$em->getConnection()->getConfiguration()
                    ->setSQLLogger(DataCollector\QueriesCollector::getInstance());

            } elseif (\XLite\Core\Config::getInstance()->XC->WebmasterKit->logSQL) {
                static::$em->getConnection()->getConfiguration()
                    ->setSQLLogger(\XLite\Logger::getInstance());

            }
        }
    }
}
