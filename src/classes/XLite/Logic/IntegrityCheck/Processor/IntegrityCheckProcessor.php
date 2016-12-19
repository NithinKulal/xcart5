<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor;

use XLite\Logic\IntegrityCheck\Processor\Entry;

/**
 * Class IntegrityCheckProcessor
 */
class IntegrityCheckProcessor
{
    /**
     * @return array
     */
    public static function getCoreData()
    {
        $coreEntry = new Entry\Core();

        return [
            'Core' => static::retrieveModifiedFiles($coreEntry)
        ];
    }

    /**
     * @return array
     */
    public static function getModulesData()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;

        $modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->search($cnd);

        $result = [];

        /** @var \XLite\Model\Module $module */
        foreach ($modules as $module) {
            $result[$module->getActualName()] = static::getModuleData($module);
        }

        return array_filter($result);
    }

    /**
     * @param \XLite\Model\Module $module
     *
     * @return array
     */
    protected static function getModuleData(\XLite\Model\Module $module)
    {
        $moduleEntry = new Entry\Module(
            $module->getActualName(),
            $module->getVersion()
        );

        return array_filter(IntegrityCheckProcessor::retrieveModifiedFiles($moduleEntry));
    }

    /**
     * @param \XLite\Logic\IntegrityCheck\Processor\Entry\IEntry $entry
     *
     * @return array
     */
    public static function retrieveModifiedFiles(Entry\IEntry $entry)
    {
        $hashes = $entry->getHashes();
        $files = $entry->getRealFiles();

        $result = [];
        $addedFiles = [];
        $removedFiles = [];

        if ($hashes) {
            /** @var \SplFileInfo $value */
            foreach ($files as $key => $value) {
                $path = \Includes\Utils\FileManager::getRelativePath($key, LC_DIR_ROOT);

                if ($hashes && array_key_exists($path, $hashes)) {
                    $expectedHash   = $hashes[$path];
                    $realHash       = \Includes\Utils\FileManager::getHash($key);

                    if ($expectedHash !== $realHash) {
                        $result[] = $path;
                    }

                } else {
                    $addedFiles[] = $path;
                }
            }

            foreach ($hashes as $path => $hash) {
                if (!\Includes\Utils\FileManager::isExists($path)) {
                    $removedFiles[] = $path;
                }
            }
        }

        return [
            'modified'  => static::postProcessFiles($result),
            'added'     => static::postProcessFiles($addedFiles),
            'removed'   => static::postProcessFiles($removedFiles),
            'errors'    => $entry->getErrors(),
        ];
    }

    /**
     * @param array $files
     *
     * @return array
     */
    protected static function postProcessFiles(array $files)
    {
        $pattern = static::getExcludedPattern();
        return array_filter($files, function($file) use ($pattern) {
            return !preg_match($pattern, $file);
        });
    }

    /**
     * @return string
     */
    protected static function getExcludedPattern()
    {
        $list = array_merge(
            ['list' => [], 'raw' => []],
            static::getCommonExludePatterns()
        );

        $toImplode = $list['raw'];

        foreach ($list['list'] as $pattern) {
            $toImplode[] = preg_quote($pattern, '/');
        }

        return  '/^(?:' . implode('|', $toImplode) . ')/Ss';
    }

    /**
     * @return array
     */
    protected static function getCommonExludePatterns()
    {
        return [
            'list'  => [],
            'raw'   => [
                ".*\/.gitattributes",
                ".*\/.gitignore",
                ".*\/?.htaccess",
            ]
        ];
    }
}
