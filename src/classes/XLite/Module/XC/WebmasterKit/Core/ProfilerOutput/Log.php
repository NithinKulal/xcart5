<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\ProfilerOutput;


use XLite\Module\XC\WebmasterKit\Core\DataCollector\MemoryPointsCollector;
use XLite\Module\XC\WebmasterKit\Core\DataCollector\QueriesCollector;

class Log
{
    const DEC_POINT = '.';
    const THOUSANDS_SEP = ' ';

    const QUERY_LIMIT_TIMES = 2;
    const QUERY_LIMIT_DURATION = 0.05;

    protected $memoryPoints;
    protected $queries;
    protected $points;
    private $messages;

    private $timeData;

    /**
     * @inheritDoc
     */
    public function __construct($points, $messages, $timeData)
    {
        $this->points = $points;
        $this->messages = $messages;
        $this->timeData = $timeData;

        $this->queries = QueriesCollector::getInstance()->getCollected();
        $this->memoryPoints = MemoryPointsCollector::getInstance()->getCollected();
    }

    /**
     * Log profile data
     *
     * @return void
     */
    public function output()
    {
        $data = $this->prepareFinalData();

        $message = <<<HTML
Profiler data

Execution time:                 {$data['execTime']} sec.
Memory usage (peak):            {$data['memoryPeak']} Mb
SQL queries count:              {$data['totalQueries']} unique / {$data['totalQueriesCount']} total
SQL queries duration:           {$data['totalQueriesTime']} sec.
Included files count:           {$data['includedFilesCount']}
Included files total size:      {$data['includedFilesCount']} Kb.
Database connect time:          {$data['dbConnectTime']} sec.
Doctrine UnitOfWork final size: {$data['unitOfWorkSize']} models

HTML;
        $message .= $this->getMessagesData();
        $message .= $this->getQueriesData();
        $message .= $this->getDoctrineUOWData();
        $message .= $this->getMemoryPointsData();
        $message .= $this->getPointsData();

        \XLite\Logger::getInstance()->logCustom('profiler', $message);
    }

    /**
     * Prepare final data
     *
     * @return array
     */
    protected function prepareFinalData()
    {
        $includedFiles = array();
        $includedFilesTotal = 0;

        foreach (get_included_files() as $file) {
            $size = intval(@filesize($file));
            $includedFiles[] = array(
                'name' => $file,
                'size' => $size
            );
            $includedFilesTotal += $size;
        }

        usort($includedFiles, array($this, 'sortCallback'));

        $result = array(
            'totalQueriesTime'   => 0,
            'totalQueriesCount'  => 0,
            'includedFilesCount' => count($includedFiles),
            'includedFilesTotal' => round($includedFilesTotal / 1024, 3),
        );

        $queries = [];
        foreach ($this->queries as $q => $d) {
            $cnt = count($d['time']);
            $sum = array_sum($d['time']);
            $queries[$q] = array(
                'count' => $cnt,
                'max'   => empty($d['time']) ? 0 : max($d['time']),
                'min'   => empty($d['time']) ? 0 : min($d['time']),
                'avg'   => (0 < $cnt) ? $sum / $cnt : 0,
                'trace' => $d['trace'],
            );
            $result['totalQueriesTime'] += $sum;
            $result['totalQueriesCount'] += $cnt;
        }
        $this->queries = $queries;

        $result['execTime'] = number_format($this->timeData['stop_time'] - $this->timeData['start_time'], 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['memoryPeak'] = round(memory_get_peak_usage() / 1024 / 1024, 3);
        $result['totalQueries'] = count($this->queries);
        $result['totalQueriesTime'] = number_format($result['totalQueriesTime'], 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['dbConnectTime'] = number_format($this->timeData['dbConnectTime'], 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['unitOfWorkSize'] = \XLite\Core\Database::getEM()->getUnitOfWork()->size();

        return $result;
    }

    /**
     * @return string
     */
    protected function getMessagesData()
    {
        $result = '';

        if (!empty($this->messages)) {
            $result .= PHP_EOL . 'Profiler Messages' . PHP_EOL;

            foreach ($this->messages as $m) {
                $result .= $m . PHP_EOL;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getQueriesData()
    {
        $result = <<<HTML

Queries log

HTML;

        if ($this->queries) {
            foreach ($this->queries as $query => $d) {
                $timesLimit = (static::QUERY_LIMIT_TIMES < $d['count'] ? '!' : ' ');
                $durationLimit = (static::QUERY_LIMIT_DURATION < $d['max'] ? '!' : ' ');

                $result .= sprintf('%7s', $d['count']) . $timesLimit . "\t"
                    . sprintf('%15s', number_format($d['max'], 4, static::DEC_POINT, static::THOUSANDS_SEP)) . $durationLimit . PHP_EOL
                    . "\t" . $query . PHP_EOL
                    . "\t" . implode(' << ', $d['trace']) . PHP_EOL;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getDoctrineUOWData()
    {
        $result = PHP_EOL . 'Doctrine Unit-of-work objects' . PHP_EOL;

        foreach (\XLite\Core\Database::getEM()->getUnitOfWork()->getIdentityMap() as $key => $ids) {
            $result .= sprintf('%62s', $key) . "\t" . count($ids) . PHP_EOL;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getMemoryPointsData()
    {
        $result = '';

        if ($this->memoryPoints) {
            $result .= PHP_EOL . 'Memory points' . PHP_EOL;

            $lastMem = 0;
            foreach ($this->memoryPoints as $d) {
                $diff = $d['memory'] - $lastMem;
                $m = number_format(round($d['memory'] / 1024 / 1024, 3), 3, static::DEC_POINT, static::THOUSANDS_SEP);
                $md = number_format(round($diff / 1024 / 1024, 3), 3, static::DEC_POINT, static::THOUSANDS_SEP);

                $result .= sprintf('%15s', $m) . "\t" . sprintf('%15s', $md) . PHP_EOL
                    . "\t" . implode(' << ', $d['trace']) . PHP_EOL;
                $lastMem = $d['memory'];
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getPointsData()
    {
        $result = '';

        if ($this->points) {
            $result .= PHP_EOL . 'Log points' . PHP_EOL;

            foreach ($this->points as $name => $d) {
                $result .= sprintf('%15s', number_format($d['time'], 4, static::DEC_POINT, static::THOUSANDS_SEP)) . PHP_EOL
                    . "\t" . $name . PHP_EOL;
            }
        }

        return $result;
    }

    /**
     * Included files statistics sorting callback
     *
     * @param array $a File info 1
     * @param array $b File info 2
     *
     * @return integer
     */
    public function sortCallback($a, $b)
    {
        $result = 0;

        if ($a['size'] != $b['size']) {
            $result = $a['size'] < $b['size'] ? 1 : -1;
        }

        return $result;
    }
}
