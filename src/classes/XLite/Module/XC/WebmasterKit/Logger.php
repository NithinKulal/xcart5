<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit;

/**
 * Logger
 */
abstract class Logger extends \XLite\Logger implements \XLite\Base\IDecorator, \Doctrine\DBAL\Logging\SQLLogger
{
    /**
     * Query data
     *
     * @var   array
     */
    protected $query;

    /**
     * Count
     *
     * @var   integer
     */
    protected $count = 0;

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql    The SQL to be executed.
     * @param array  $params The SQL parameters.
     * @param array  $types  The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->query = array(
            'sql'    => $sql,
            'params' => $params,
            'start'  => microtime(true),
        );
    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        $duration = microtime(true) - $this->query['start'];

        $params = array();
        if ($this->query['params']) {
            foreach ($this->query['params'] as $v) {
                $params[] = var_export($v, true);
            }
        }

        $this->count++;

        $this->logCustom(
            'sql',
            'Query #' . $this->count . ': ' . $this->query['sql'] . PHP_EOL
            . ($params ? 'Parameters: ' . PHP_EOL . "\t" . implode(PHP_EOL . "\t", $params) . PHP_EOL : '')
            . 'Duration: ' . round($duration, 4) . 'sec.' . PHP_EOL
            . 'Doctrine UnitOfWork size: ' . \XLite\Core\Database::getEM()->getUnitOfWork()->size(),
            true,
            4
        );

        unset($this->query);
    }

}

