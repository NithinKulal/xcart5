<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\QuickData;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    const STEP_TTL = 20;

    /**
     * Processing chunk length
     */
    const CHUNK_LENGTH = 100;

    public static function initializeCounter()
    {
        static::setCounter(0);
    }

    public static function setCounter($count)
    {
        $string = serialize(
            array('count' => $count)
        );
        \Includes\Utils\FileManager::write(
            static::getFilePath(),
            '; <' . '?php /*' . PHP_EOL . $string . '; */ ?' . '>'
        );
    }

    public static function getCounter()
    {
        $data = \Includes\Utils\FileManager::read(static::getFilePath());

        if ($data) {
            $data = substr($data, strlen('; <' . '?php /*' . PHP_EOL), strlen('; */ ?' . '>') * -1);
            $data = unserialize($data);
        }

        return ($data && is_array($data) && isset($data['count']))
            ? intval($data['count'])
            : 0;
    }

    /**
     * Check if quick data calculation allowed
     *
     * @return boolean
     */
    public static function isCalculateCacheAllowed()
    {
        $config = \XLite\Core\Config::getInstance()->CacheManagement;

        return $config && $config->quick_data_rebuilding;
    }

    /**
     * Get file path with fixtures paths
     *
     * @return string
     */
    protected static function getFilePath()
    {
        return LC_DIR_VAR . '.quickData.php';
    }

    /**
     * Execute certain hook handle
     *
     * @return void
     */
    public function executeHookHandler()
    {
        if (static::isCalculateCacheAllowed()
            && \Includes\Decorator\Utils\CacheInfo::get('rebuildBlockMark')
        ) {
            \XLite\Core\Database::getRepo('XLite\Model\Category')->correctCategoriesStructure();

            $i = static::getCounter();
            do {
                $processed = \XLite\Core\QuickData::getInstance()->updateChunk($i, static::CHUNK_LENGTH);
                if (0 < $processed) {
                    \XLite\Core\Database::getEM()->clear();
                }
                $i += $processed;
                static::setCounter($i);
                \Includes\Utils\Operator::showMessage('.', false, true);

            } while (0 < $processed && !\Includes\Decorator\Utils\CacheManager::isTimeExceeds(static::STEP_TTL));
        }
    }
}
