<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Console;

/**
 * Cron controller
 */
class Cron extends \XLite\Controller\Console\AConsole
{
    /**
     * Time limit (seconds)
     *
     * @var integer
     */
    protected $timeLimit = 600;

    /**
     * Memory limit (bytes)
     *
     * @var integer
     */
    protected $memoryLimit = 4000000;

    /**
     * Memory limit from memory_limit PHP setting (bytes)
     *
     * @var integer
     */
    protected $memoryLimitIni;

    /**
     * Sleep time
     *
     * @var integer
     */
    protected $sleepTime = 3;

    /**
     * Start time 
     * 
     * @var integer
     */
    protected $startTime;

    /**
     * Preprocessor for no-action
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->startTime = time();
        $this->startMemory = memory_get_usage(true);
        $this->memoryLimitIni = \XLite\Core\Converter::convertShortSize(ini_get('memory_limit') ?: '16M');

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Task')->findAll() as $task) {
            if (!$task->isExpired()) {
                continue;
            }

            $runner = $task->getOwnerInstance();

            if ($runner) {
                $this->runRunner($runner);
            }

            sleep($this->sleepTime);

            if (!$this->checkThreadResource()) {
                $time = gmdate('H:i:s', \XLite\Core\Converter::time() - $this->startTime);
                $memory = \XLite\Core\Converter::formatFileSize(memory_get_usage(true));
                $this->printContent('Step is interrupted (time: ' . $time . '; memory usage: ' . $memory . ')');

                break;
            }
        }
    }

    /**
     * Check thread resource 
     * 
     * @return boolean
     */
    protected function checkThreadResource()
    {
        return time() - $this->startTime < $this->timeLimit
            && $this->memoryLimitIni - memory_get_usage(true) > $this->memoryLimit;
    }

    /**
     * Run runner 
     * 
     * @param \XLite\Core\Task\ATask $runner Runner
     *  
     * @return void
     */
    protected function runRunner(\XLite\Core\Task\ATask $runner)
    {
        $silence = !$runner->getTitle();
        if ($runner && $runner->isReady() && !$runner->isRunning()) {
            if (!$silence) {
                $this->printContent($runner->getTitle() . ' ... ');
            }

            $runner->run();

            if (!$silence) {
                $this->printContent($runner->getMessage() ?: 'done');
            }
        } elseif ($runner->isRunning()) {
            $msg = !$runner->shouldRunIfCrashed()
                ? '| Task will not be restarted because previous attempt has failed. Remove lock files manually to start the task'
                : '';
            $this->printContent($runner->getTitle() . ' ... Already running ' . $msg);
        }

        if (!$silence) {
            $this->printContent(PHP_EOL);
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
