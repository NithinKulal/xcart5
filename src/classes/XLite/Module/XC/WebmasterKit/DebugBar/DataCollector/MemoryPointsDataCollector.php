<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Memory points data collector
 */
class MemoryPointsDataCollector extends DataCollector implements Renderable
{
    const DEC_POINT     = '.';
    const THOUSANDS_SEP = ' ';

    protected $memoryPoints;

    public function collect()
    {
        $this->memoryPoints = \XLite\Module\XC\WebmasterKit\Core\Profiler::getMemoryPoints();
        $points = [];

        $lastPoint = 0;
        foreach ($this->memoryPoints as $point) {
            $points[] = [
                'memory'        => $this->formatMemoryValue($point['memory']),
                'memoryDiff'    => $this->formatMemoryValue($point['memory'] - $lastPoint),
                'stacktrace'    => implode(' << ', $point['trace'])
            ];
            $lastPoint = $point['memory'];
        }

        return [
            'points'    => $points
        ];
    }

    /**
     * Format given value in bytes to value in MB
     *
     * @param integer $value Value in bytes
     *
     * @return string
     */
    protected function formatMemoryValue($value)
    {
        return number_format(
            round($value / 1024 / 1024, 3),
            3,
            static::DEC_POINT,
            static::THOUSANDS_SEP
        );
    }

    public function getName()
    {
        return 'memory_points';
    }

    public function getWidgets()
    {
        return [
            "memory_points" => [
                "icon"    => "tasks",
                "widget"  => "PhpDebugBar.XCartWidgets.MemoryPointsWidget",
                "map"     => "memory_points.points",
                "default" => "{}",
            ],
        ];
    }
}
