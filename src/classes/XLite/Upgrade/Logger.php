<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Upgrade;

/**
 * Logger
 */
class Logger extends \XLite\Base\Singleton
{
    /**
     * Clear log file
     *
     * @return boolean
     */
    public function clear()
    {
        return \Includes\Utils\FileManager::deleteFile($this->getLogFile());
    }

    /**
     * Return log file name
     *
     * @return string
     */
    public function getLogFile()
    {
        return LC_DIR_LOG . 'upgrade.log.' . date('Y-m-d') . '.php';
    }

    /**
     * Return last log file name
     *
     * @return string
     */
    public function getLastLogFile()
    {
        $result = null;

        $filter = new \Includes\Utils\FileFilter(
            LC_DIR_LOG,
            '/\/upgrade\.log\.\d{4}-\d{2}-\d{2}\.php$/',
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        $paths = array();

        foreach ($filter->getIterator() as $file) {
            $paths[] = $file->getRealPath();
        }

        if ($paths) {
            arsort($paths);
            $result = reset($paths);
        }

        return $result;
    }

    /**
     * Return link to view the log file
     *
     * @return string
     */
    public function getLogURL()
    {
        return \XLite\Core\Converter::buildURL('upgrade', 'view_log_file');
    }

    /**
     * Add message to the log
     *
     * @param string  $message        Message text
     * @param array   $args           Arguments to substitute OPTIONAL
     * @param boolean $showTopMessage Flag OPTIONAL
     *
     * @return void
     */
    public function logInfo($message, array $args = array(), $showTopMessage = false)
    {
        $this->log($message, $args, $showTopMessage, \XLite\Core\TopMessage::INFO);
    }

    /**
     * Add message to the log
     *
     * @param string  $message        Message text
     * @param array   $args           Arguments to substitute OPTIONAL
     * @param boolean $showTopMessage Flag OPTIONAL
     *
     * @return void
     */
    public function logWarning($message, array $args = array(), $showTopMessage = false)
    {
        $this->log($message, $args, $showTopMessage, \XLite\Core\TopMessage::WARNING);
    }

    /**
     * Add message to the log
     *
     * @param string  $message        Message text
     * @param array   $args           Arguments to substitute OPTIONAL
     * @param boolean $showTopMessage Flag OPTIONAL
     *
     * @return void
     */
    public function logError($message, array $args = array(), $showTopMessage = false)
    {
        $this->log($message, $args, $showTopMessage, \XLite\Core\TopMessage::ERROR);
    }

    /**
     * Add message to the log
     *
     * @param string  $message        Message text
     * @param array   $args           Arguments to substitute OPTIONAL
     * @param boolean $showTopMessage Flag OPTIONAL
     * @param string  $topMessageType \XLite\Core\TopMessage class constant OPTIONAL
     *
     * @return void
     */
    protected function log($message, array $args = array(), $showTopMessage = false, $topMessageType = null)
    {
        // Write to file
        $this->write($this->getPrefix($topMessageType) . static::t($message, $args));

        // Show to admin
        if ($showTopMessage) {
            \XLite\Core\TopMessage::getInstance()->add($this->getTopMessage($message), $args, null, $topMessageType);
        }
    }

    /**
     * Write message to the file
     *
     * @param string $message Message text
     *
     * @return void
     */
    protected function write($message)
    {
        \XLite\Logger::getInstance()->checkLogSecurityHeader($this->getLogFile());

        \Includes\Utils\FileManager::write($this->getLogFile(), $message . PHP_EOL, FILE_APPEND);
    }

    /**
     * Get message prefix
     *
     * @param string $type Prefix type
     *
     * @return string
     */
    protected function getPrefix($type)
    {
        return '[' . $type . ', ' . date('M d Y H:i:s') . '] ';
    }

    /**
     * Prepare message to display (not log)
     *
     * @param string $message Message text
     *
     * @return string
     */
    protected function getTopMessage($message)
    {
        return $message . '<p /><a target="_blank" href=' . $this->getLogURL() . '><u>' . static::t('See log file for details') . '</u></a>';
    }
}
