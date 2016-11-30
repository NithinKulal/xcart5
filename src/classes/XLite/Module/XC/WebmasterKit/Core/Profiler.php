<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core;

use XLite\Module\XC\WebmasterKit\Core\DataCollector\MessagesCollector;
use XLite\Module\XC\WebmasterKit\Core\DataCollector\PointsCollector;

/**
 * Profiler
 */
class Profiler extends \XLite\Base\Singleton
{
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
    protected $timeData = [
        'start_time'        => 0,
        'stop_time'         => 0,
        'dbConnectTime'     => 0,
    ];

    /**
     * Profiler should not start on these targets
     *
     * @var array
     */
    protected $disallowedTargets = array(
        'image',
    );

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
     * Log SQL queries
     *
     * @param string $sql    Query
     * @param array  $params Query arguments OPTIONAL
     *
     * @return void
     */
    public function logSQL($sql, array $params = null)
    {
        DataCollector\QueriesCollector::getInstance()->startQuery($sql, $params);
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
        PointsCollector::getInstance()->addPoint($timePoint, $additional);
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
        MessagesCollector::getInstance()->addMessage($message, $this->timeData['start_time']);
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->enabled = $this->getStartupFlag();
        $this->timeData['start_time'] = $_SERVER['REQUEST_TIME'];

        static::$markTemplates = (bool) \XLite\Core\Config::getInstance()->XC->WebmasterKit->markTemplates
            && \XLite\Core\Request::getInstance()->isGet();
    }

    /**
     * Destructor
     *
     * @return void
     */
    public function __destruct()
    {
        $this->timeData['stop_time'] = microtime(true);

        $output = null;

        $points =  PointsCollector::getInstance()->getCollected();
        $messages =  DataCollector\MessagesCollector::getInstance()->getCollected();

        if ($this->isDisplay()) {
            $output = new ProfilerOutput\Html($points, $messages);

        } elseif (\XLite\Core\Config::getInstance()->XC->WebmasterKit->logProfiler) {
            $output = new ProfilerOutput\Log($points, $messages, $this->timeData);
        }

        if ($output) {
            $output->output();
        }
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

        } elseif (PointsCollector::getInstance()->hasPoint($name)) {
            $result = PointsCollector::getInstance()->getPointData($name);

        } else {
            $result = null;
        }

        return $result;
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
}
