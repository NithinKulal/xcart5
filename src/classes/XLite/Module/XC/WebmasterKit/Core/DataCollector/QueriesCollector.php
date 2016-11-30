<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\DataCollector;


use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

class QueriesCollector extends \Xlite\Base\Singleton implements \Doctrine\DBAL\Logging\SQLLogger
{
    const TRACE_BEGIN = 3;
    const TRACE_LENGTH = 16;

    /**
     * Current query
     *
     * @var string
     */
    protected $currentQuery;

    /**
     * Last time
     *
     * @var float
     */
    protected $lastTime = 0;

    /**
     * List of executed queries
     *
     * @var array
     */
    protected $queries = array();

    /**
     * @param null $name
     *
     * @return array
     */
    public function getCollected($name = null)
    {
        return $name && isset($this->queries[$name])
            ? $this->queries[$name]
            : $this->queries;
    }

    /**
     * Logs a SQL statement somewhere
     *
     * @param string $sql    The SQL to be executed
     * @param array  $params The SQL parameter OPTIONAL
     * @param array  $types  The SQL parameter types OPTIONAL
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null, $backtrace = null)
    {
        if (!$this->hasQuery($sql)) {
            MemoryPointsCollector::getInstance()->addMemoryPoint();
        }

        $this->addQuery($sql, $backtrace);
        $this->currentQuery = $sql;
    }

    /**
     * Mark the last started query as stopped. This can be used for timing of queries
     *
     * @return void
     */
    public function stopQuery()
    {
        $this->setQueryTime($this->currentQuery);
    }

    /**
     * Add query to log
     *
     * @param string $query Query
     *
     * @return void
     */
    protected function addQuery($query, $backtrace = null)
    {
        $this->lastTime = microtime(true);

        // Uncomment if you want to truncate queries
        /* if (strlen($query)>300) {
            $query = substr($query, 0, 300) . ' ...';

        } */

        $settingsMgr = new DebugBarSettingsManager();

        if ($settingsMgr->areSqlQueryStacktracesEnabled()) {
            $backtrace = $backtrace ?: $this->getBackTrace();
        }

        if (!isset($this->queries[$query])) {
            $this->queries[$query] = array(
                'time'  => array(),
                'trace' => $backtrace ?: [],
            );
        }
    }
    
    public function hasQuery($query)
    {
        return isset($this->queries[$query]);
    }

    /**
     * Set query time
     *
     * @param string $query Query
     *
     * @return void
     */
    protected function setQueryTime($query)
    {
        if (isset($this->queries[$query])) {
            $this->queries[$query]['time'][] = microtime(true) - $this->lastTime;
        }
    }

    /**
     * Get back trace
     *
     * @return array
     */
    protected function getBackTrace()
    {
        $trace = array();

        foreach (debug_backtrace(false) as $l) {
            if (isset($l['function']) && isset($l['class']) && isset($l['line'])) {
                $trace[] = $l['class'] . '#' . $l['function'] . '():' . $l['line'];

            } elseif (isset($l['function']) && isset($l['line'])) {
                $trace[] = $l['function'] . '():' . $l['line'];

            } elseif (isset($l['file']) && isset($l['line'])) {
                $trace[] = str_replace(
                        array(LC_DIR_COMPILE, LC_DIR_ROOT),
                        array('', ''),
                        $l['file']
                    ) . ':' . $l['line'];

            }
        }

        return array_slice($trace, static::TRACE_BEGIN, static::TRACE_LENGTH);
    }
}