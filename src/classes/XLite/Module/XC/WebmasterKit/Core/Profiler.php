<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core;

/**
 * Profiler
 */
class Profiler extends \XLite\Base\Singleton implements \Doctrine\DBAL\Logging\SQLLogger
{
    const QUERY_LIMIT_TIMES = 2;
    const QUERY_LIMIT_DURATION = 0.05;

    const TRACE_BEGIN = 3;
    const TRACE_LENGTH = 16;

    const DEC_POINT     = '.';
    const THOUSANDS_SEP = ' ';


    /**
     * List of executed queries
     *
     * @var array
     */
    protected static $queries = array();

    /**
     * List of memory measuring points
     *
     * @var array
     */
    protected static $memoryPoints = array();

    /**
     * Mark templates flag
     *
     * @var   boolean
     */
    protected static $markTemplates = false;

    /**
     * Enabled flag
     *
     * @var boolean
     */
    protected $enabled = false;

    /**
     * Start time
     *
     * @var float
     */
    protected $start_time = null;

    /**
     * Stop time
     *
     * @var float
     */
    protected $stop_time = null;

    /**
     * Included files list
     *
     * @var array
     */
    protected $includedFiles = array();

    /**
     * Included files total size
     *
     * @var integer
     */
    protected $includedFilesTotal = 0;

    /**
     * Included files count
     *
     * @var integer
     */
    protected $includedFilesCount = 0;

    /**
     * Last time
     *
     * @var float
     */
    protected $lastTime = 0;

    /**
     * Time points
     *
     * @var array
     */
    protected $points = array();

    /**
     * Profiler should not start on these targets
     *
     * @var array
     */
    protected $disallowedTargets = array(
        'image',
    );

    /**
     * Use xdebug stack trace
     *
     * @var boolean
     */
    protected static $useXdebugStackTrace = false;

    /**
     * Current query
     *
     * @var string
     */
    protected $currentQuery;

    /**
     * List of plain text messages
     *
     * @var array
     */
    protected $messages = array();

    public static function getMemoryPoints()
    {
        return static::$memoryPoints;
    }

    /**
     * Check - templates profiling mode is enabled or not
     *
     * @return boolean
     */
    public static function markTemplatesEnabled()
    {
        return static::$markTemplates;
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->start($this->getStartupFlag());
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        $this->stop();
    }

    /**
     * Getter
     *
     * @param string $name Peroperty name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ('enabled' == $name) {
            $result = $this->enabled;

        } elseif (isset($this->points[$name])) {

            $result = isset($this->points[$name]['end'])
                ? ($this->points[$name]['end'] - $this->points[$name]['start'])
                : 0;

        } else {
            $result = null;
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

    /**
     * Log SQL queries
     *
     * @param string $sql    Query
     * @param array  $params Query arguments OPTIONAL
     *
     * @return void
     */
    public function logSQL($sql, array $params = null)
    {
        $this->addQuery($sql);
    }

    /**
     * Add query to log
     *
     * @param string $query Query
     *
     * @return void
     */
    public function addQuery($query)
    {
        $this->lastTime = microtime(true);

        // Uncomment if you want to truncate queries
        /* if (strlen($query)>300) {
            $query = substr($query, 0, 300) . ' ...';

        } */

        if (!isset(static::$queries[$query])) {
            static::$queries[$query] = array(
                'time' => array(),
                'trace' => $this->getBackTrace(),
            );
            $this->addMemoryPoint();
        }
    }

    /**
     * Set query time
     *
     * @param string $query Query
     *
     * @return void
     */
    public function setQueryTime($query)
    {
        if (isset(static::$queries[$query])) {
            static::$queries[$query]['time'][] = microtime(true) - $this->lastTime;
        }
    }

    /**
     * Add memory measure point
     *
     * @return void
     */
    public function addMemoryPoint()
    {
        static::$memoryPoints[] = array(
            'memory' => memory_get_usage(),
            'trace' => $this->getBackTrace(),
        );
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
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->addQuery($sql);
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
     * Log same time range
     *
     * @param string  $timePoint  Time range name
     * @param boolean $additional Additional metric flag OPTIONAL
     *
     * @return void
     */
    public function log($timePoint, $additional = false)
    {
        if (!isset($this->points[$timePoint])) {
            $this->points[$timePoint] = array(
                'start' => microtime(true),
                'open'  => true,
                'time'  => 0,
            );
            if (static::$useXdebugStackTrace) {
                @xdebug_start_trace(
                    LC_DIR_LOG . \Includes\Utils\FileManager::sanitizeFilename($timePoint) . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        } elseif ($this->points[$timePoint]['open']) {

            $range = microtime(true) - $this->points[$timePoint]['start'];
            if ($additional) {
                $this->points[$timePoint]['time'] += $range;
            } else {
                $this->points[$timePoint]['time'] = $range;
            }
            $this->points[$timePoint]['open'] = false;

            if (static::$useXdebugStackTrace) {
                @xdebug_stop_trace();
            }

        } else {

            $this->points[$timePoint]['start'] = microtime(true);
            $this->points[$timePoint]['open'] = true;

            if (static::$useXdebugStackTrace) {
                @xdebug_start_trace(
                    LC_DIR_VAR . 'log' . LC_DS . \Includes\Utils\FileManager::sanitizeFilename($timePoint) . '.' . microtime(true),
                    XDEBUG_TRACE_COMPUTERIZED
                );
            }

        }
    }

    /**
     * Add new message
     *
     * @param string $message Message text
     *
     * @return void
     */
    public function addMessage($message)
    {
        $this->messages[] = '[' . number_format(microtime(true) - $this->start_time, 4) . ']: ' . $message;
    }


    /**
     * There are some targets which are not require profiler
     *
     * @return boolean
     */
    protected function isTargetAllowed()
    {
        return !in_array(\XLite\Core\Request::getInstance()->target, $this->disallowedTargets);
    }

    /**
     * Only admin users are allowed to use profiler
     *
     * @return boolean
     */
    protected function isUserAllowed()
    {
        $result = true;

        $allowedType = \XLite\Core\Config::getInstance()->XC->WebmasterKit->debugBarAllowedUsers;
        if ($allowedType === 'admin') {
            $auth = \XLite\Core\Auth::getInstance();
            $result = $auth->getProfile() && $auth->getProfile()->isAdmin();
        }

        return $result;
    }

    /**
     * getStartupFlag
     *
     * @return boolean
     */
    public function getStartupFlag()
    {
        return $this->isTargetAllowed()
            && $this->isUserAllowed()
            && !\XLite\Core\Request::getInstance()->isPost()
            && !\XLite\Core\Request::getInstance()->isCLI()
            && !\XLite\Core\Request::getInstance()->isAJAX()
            && !\Includes\Decorator\Utils\CacheManager::isRebuildNeeded()
            && (\XLite\Core\Config::getInstance()->XC->WebmasterKit->debugBarEnabled
                || \XLite\Core\Config::getInstance()->XC->WebmasterKit->logProfiler);
    }

    /**
     * Start profiler
     *
     * @param boolean $start Enable flag
     *
     * @return void
     */
    protected function start($start)
    {
        $this->enabled = !empty($start);
        $this->start_time = $_SERVER['REQUEST_TIME'];

        static::$useXdebugStackTrace = function_exists('xdebug_start_trace')
            && \XLite\Core\Config::getInstance()->XC->WebmasterKit->xdebugLogTrace;

        static::$markTemplates = (bool) \XLite\Core\Config::getInstance()->XC->WebmasterKit->markTemplates
            && \XLite\Core\Request::getInstance()->isGet();
    }

    /**
     * Stop profiler
     *
     * @return void
     */
    protected function stop()
    {
        if ($this->isDisplay()) {

            $this->display();

        } elseif (\XLite\Core\Config::getInstance()->XC->WebmasterKit->logProfiler) {
            $this->logProfileData();
        }
    }

    /**
     * Check - display profiler log or not
     * 
     * @return boolean
     */
    protected function isDisplay()
    {
        return $this->enabled
            && !\XLite\Core\Config::getInstance()->XC->WebmasterKit->logProfiler
            && !\XLite\Core\Request::getInstance()->isPopup
            && !\XLite::getController()->get('silent')
            && \XLite\Core\Request::getInstance()->isGet();
        
    }

    /**
     * Prepare final data 
     * 
     * @return array
     */
    protected function prepareFinalData()
    {
        $this->stop_time = microtime(true);

        $this->includedFiles = array();
        $this->includedFilesTotal = 0;

        foreach (get_included_files() as $file) {
            $size = intval(@filesize($file));
            $this->includedFiles[] = array(
                'name' => $file,
                'size' => $size
            );
            $this->includedFilesTotal += $size;
        }
        $this->includedFilesCount = count($this->includedFiles);

        usort($this->includedFiles, array($this, 'sortCallback'));

        $result = array(
            'totalQueriesTime'   => 0,
            'totalQueriesCount' => 0,
        );
        foreach (static::$queries as $q => $d) {
            $cnt = count($d['time']);
            $sum = array_sum($d['time']);
            static::$queries[$q] = array(
                'count' => $cnt,
                'max'   => empty($d['time']) ? 0 : max($d['time']),
                'min'   => empty($d['time']) ? 0 : min($d['time']),
                'avg'   => (0 < $cnt) ? $sum / $cnt : 0,
                'trace' => $d['trace'],
            );
            $result['totalQueriesTime'] += $sum;
            $result['totalQueriesCount'] += $cnt;
        }

        $result['execTime'] = number_format($this->stop_time - $this->start_time, 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['memoryPeak'] = round(memory_get_peak_usage() / 1024 / 1024, 3);
        $result['totalQueries'] = count(static::$queries);
        $result['totalQueriesTime'] = number_format($result['totalQueriesTime'], 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['dbConnectTime'] = number_format($this->dbConnectTime, 4, static::DEC_POINT, static::THOUSANDS_SEP);
        $result['unitOfWorkSize'] = \XLite\Core\Database::getEM()->getUnitOfWork()->size();

        $this->includedFilesTotal = round($this->includedFilesTotal / 1024, 3);

        return $result;
    }

    /**
     * Display profiler report
     *
     * @return void
     */
    protected function display()
    {
        if (!empty($this->messages)) {
            $html = <<<HTML
<br /><br />
<table cellspacing="0" cellpadding="3" border="1" style="width: auto; top: 0; z-index: 10000; background-color: #fff;">
    <caption style="font-weight: bold; text-align: left;">Profiler Messages</caption>
HTML;

            foreach ($this->messages as $message) {
                $html .= <<<HTML
<tr><td>$message</td></tr>
HTML;
            }

            $html .= <<<HTML
</table>
HTML;

            echo ($html);
        }

        if ($this->points) {
            $html = <<<HTML
<table cellspacing="0" cellpadding="3" border="1" style="width: auto;">
    <caption style="font-weight: bold; text-align: left;">Log points</caption>
    <tr>
        <th>Duration, sec.</th>
        <th>Point name</th>
    </tr>
HTML;
            echo ($html);

            foreach ($this->points as $name => $d) {
                echo (
                    '<tr><td>'
                    . number_format($d['time'], 4, static::DEC_POINT, static::THOUSANDS_SEP)
                    . '</td><td>'
                    . $name
                    . '</td></tr>'
                );
            }

            echo ('</table>');
        }

        echo ('</div>');
    }

    /**
     * Log profile data 
     * 
     * @return void
     */
    protected function logProfileData()
    {
        $data = $this->prepareFinalData();

        $message = <<<HTML
Profiler data

Execution time:                 {$data['execTime']} sec.
Memory usage (peak):            {$data['memoryPeak']} Mb
SQL queries count:              {$data['totalQueries']} unique / {$data['totalQueriesCount']} total
SQL queries duration:           {$data['totalQueriesTime']} sec.
Included files count:           $this->includedFilesCount
Included files total size:      $this->includedFilesTotal Kb.
Database connect time:          {$data['dbConnectTime']} sec.
Doctrine UnitOfWork final size: {$data['unitOfWorkSize']} models

HTML;

        if (!empty($this->messages)) {
            $message .= PHP_EOL . 'Profiler Messages' . PHP_EOL;

            foreach ($this->messages as $m) {
                $message .= $m . PHP_EOL;
            }
        }

        if (static::$queries) {

            $message .= <<<HTML

Queries log

HTML;
            foreach (static::$queries as $query => $d) {
                $timesLimit = (static::QUERY_LIMIT_TIMES < $d['count'] ? '!' : ' ');
                $durationLimit = (static::QUERY_LIMIT_DURATION < $d['max'] ? '!' : ' ');

                $message .= sprintf('%7s', $d['count']) . $timesLimit . "\t"
                    . sprintf('%15s',  number_format($d['max'], 4, static::DEC_POINT, static::THOUSANDS_SEP)) . $durationLimit . PHP_EOL
                    . "\t" . $query . PHP_EOL
                    . "\t" . implode(' << ', $d['trace']) . PHP_EOL;
            }
        }

        // Doctrine Unit-of-work objects
        $message .= PHP_EOL . 'Doctrine Unit-of-work objects' . PHP_EOL;

        foreach (\XLite\Core\Database::getEM()->getUnitOfWork()->getIdentityMap() as $key => $ids) {
            $message .= sprintf('%62s', $key) . "\t" . count($ids) . PHP_EOL;
        }

        // Memory points
        if (static::$memoryPoints) {
            $message .= PHP_EOL . 'Memory points' . PHP_EOL;

            $lastMem = 0;
            foreach (static::$memoryPoints as $d) {
                $diff = $d['memory'] - $lastMem;
                $m = number_format(round($d['memory'] / 1024 / 1024, 3), 3, static::DEC_POINT, static::THOUSANDS_SEP);
                $md = number_format(round($diff / 1024 / 1024, 3), 3, static::DEC_POINT, static::THOUSANDS_SEP);

                $message .= sprintf('%15s', $m) . "\t" . sprintf('%15s', $md) . PHP_EOL
                    . "\t" . implode(' << ', $d['trace']) . PHP_EOL;
                $lastMem = $d['memory'];
            }
        }

        if ($this->points) {
            $message .= PHP_EOL . 'Log points' . PHP_EOL;

            foreach ($this->points as $name => $d) {
                $message .= sprintf('%15s', number_format($d['time'], 4, static::DEC_POINT, static::THOUSANDS_SEP)) . PHP_EOL
                    . "\t" . $name . PHP_EOL;
            }
        }

        \XLite\Logger::getInstance()->logCustom('profiler', $message);
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
