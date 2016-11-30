<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use XLite\Module\XC\WebmasterKit\Core\DataCollector\QueriesCollector;
use XLite\Module\XC\WebmasterKit\Logic\DebugBarSettingsManager;

/**
 * Doctrine queries data collector
 */
class DoctrineCollectorSimple extends DataCollector implements Renderable, AssetProvider
{
    const QUERY_LIMIT_TIMES     = 2;
    const QUERY_LIMIT_DURATION  = 0.05;

    protected $queriesBacktraces = [];
    /**
     * @var QueriesCollector
     */
    private $queriesCollector;

    public function __construct(QueriesCollector $queriesCollector)
    {
        $this->queriesCollector = $queriesCollector;
    }


    public function collect()
    {
        $collected = $this->emulateParentCollect();

        if ($this->isCompactMode()) {
            $unique = $this->getUniqueQueries($collected['statements'], $this->withParams());
            $collected['statements']            = array_values($unique);
            $collected['nb_statements']         = count($unique);
        }

        $settingsMgr = new DebugBarSettingsManager();

        if ($settingsMgr->areSqlQueryStacktracesEnabled()) {
            foreach ($collected['statements'] as $key => $statement) {
                $collected['statements'][$key]['backtrace'] = QueriesCollector::getInstance()->hasQuery($statement['sql'])
                    ? array_slice(QueriesCollector::getInstance()->getCollected($statement['sql'])['trace'], 2)
                    : null;
            }
        }

        return $collected;
    }
    
    protected function emulateParentCollect()
    {
        $queries = array();
        $totalCount = 0;
        $totalExecTime = 0;

        foreach ($this->queriesCollector->getCollected() as $sql => $q) {
            $params = isset($q['params'])
                ? $q['params']
                : [];
            $timeTotal = array_reduce($q['time'], function($carry, $timeItem){
                return $carry + $timeItem;
            }, 0);
            $duration = $timeTotal / count($q['time']);
            $totalCount += count($q['time']);

            $queries[] = array(
                'sql'           => $sql,
                'params'        => (object) $params,
                'duration'      => $duration,
                'duration_str'  => $this->formatDuration($duration)
            );
            $totalExecTime += $duration;
        }

        return array(
            'nb_statements'             => count($queries),
            'nb_total_statements'       => $totalCount,
            'accumulated_duration'      => $totalExecTime,
            'accumulated_duration_str'  => $this->formatDuration($totalExecTime),
            'statements'                => $queries
        );
    }

    protected function getUniqueQueries($statements, $withParams = false)
    {
        $unique = [];

        foreach ($statements as $statement) {
            $hashBase = serialize($statement['sql']);

            if ($withParams) {
                $hashBase .= serialize($statement['params'] ?: []);
            }
            $hash = md5($hashBase);
            if (array_key_exists($hash, $unique)) {
                $unique[$hash]['count']++;
            } else {
                $unique[$hash] = $statement;
                $unique[$hash]['count'] = 1;
            }

            $unique[$hash]['duration'] = max($unique[$hash]['duration'], $unique[$hash]['duration']);
            if ($unique[$hash]['duration'] > static::QUERY_LIMIT_DURATION) {
                $unique[$hash]['duration_warning'] = true;
            }

            if ($unique[$hash]['count'] > static::QUERY_LIMIT_TIMES) {
                $unique[$hash]['count_warning'] = true;
            }
        }

        return $unique;
    }

    protected function withParams()
    {
        return false;
    }

    protected function isCompactMode()
    {
        return true;
    }

    public function getWidgets()
    {
        $widgets = array(
            "database" => array(
                "icon" => "arrow-right",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "doctrine",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "doctrine.nb_statements",
                "default" => 0
            )
        );

        if ($this->isCompactMode()
            && isset($widgets['database']['widget'])
        ) {
            $widgets['database']['widget'] = 'PhpDebugBar.XCartWidgets.SQLQueriesCompactWidget';
        }

        return $widgets;
    }

    /**
     * Returns the unique name of the collector
     *
     * @return string
     */
    function getName()
    {
        return 'doctrine';
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return array(
            'css' => 'widgets/sqlqueries/widget.css',
            'js' => 'widgets/sqlqueries/widget.js'
        );
    }
}
