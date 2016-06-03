<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

/**
 * Doctrine queries data collector
 */
class DoctrineCollectorSimple extends \DebugBar\Bridge\DoctrineCollector
{
    const QUERY_LIMIT_TIMES     = 2;
    const QUERY_LIMIT_DURATION  = 0.05;

    protected $queriesBacktraces = [];

    public function __construct($debugStackOrEntityManager)
    {
        parent::__construct($debugStackOrEntityManager);

        $this->debugStack->addStartQueryObserver([$this, 'startQuery']);
    }


    public function startQuery($sql, $params, $types)
    {
        $this->queriesBacktraces[$sql] = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 3);
    }

    public function collect()
    {
        $collected = parent::collect();

        if ($this->isCompactMode()) {
            $unique = $this->getUniqueQueries($collected['statements'], $this->withParams());
            $collected['nb_total_statements']   = count($collected['statements']);
            $collected['statements']            = array_values($unique);
            $collected['nb_statements']         = count($unique);
        }

        foreach ($collected['statements'] as $key => $statement) {
            $collected['statements'][$key]['backtrace'] = $this->queriesBacktraces[$statement['sql']];
        }

        return $collected;
    }

    protected function getUniqueQueries($statements, $withParams = false)
    {
        $unique = [];

        foreach ($statements as $statement) {
            $hashBase = serialize($statement['sql']);

            if ($withParams) {
                $hashBase .= serialize($statement['params']);
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
        $widgets = parent::getWidgets();

        if ($this->isCompactMode()
            && isset($widgets['database']['widget'])
        ) {
            $widgets['database']['widget'] = 'PhpDebugBar.XCartWidgets.SQLQueriesCompactWidget';
        }

        return $widgets;
    }
}
