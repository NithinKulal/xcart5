<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\DataCollector;


class MemoryPointsCollector extends \XLite\Base\Singleton
{
    const TRACE_BEGIN = 3;
    const TRACE_LENGTH = 16;

    /**
     * List of memory measuring points
     *
     * @var array
     */
    protected $memoryPoints = [];

    /**
     * @return array
     */
    public function getCollected()
    {
        return $this->memoryPoints;
    }

    /**
     * Add memory measure point
     *
     * @return void
     */
    public function addMemoryPoint($backtrace = null)
    {
        $this->memoryPoints[] = array(
            'memory' => memory_get_usage(),
            'trace' => $backtrace ?: $this->getBackTrace(),
        );
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
            if (isset($l['file']) && isset($l['line'])) {
                $trace[] = str_replace(
                        array(LC_DIR_COMPILE, LC_DIR_ROOT),
                        array('', ''),
                        $l['file']
                    ) . ':' . $l['line'];

            } elseif (isset($l['function']) && isset($l['line'])) {
                $trace[] = $l['function'] . '():' . $l['line'];
            }
        }

        return array_slice($trace, static::TRACE_BEGIN, static::TRACE_LENGTH);
    }
}