<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\ReadyToInstall;

/**
 * EntriesList
 */
class EntriesList extends \XLite\View\Upgrade\Step\ReadyToInstall\AReadyToInstall
{
    /**
     * List of files and dirs with wrong permissions
     *
     * @var array
     */
    protected $wrongPermissions;

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/entries_list';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.entries_list';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Downloaded components';
    }

    /**
     * Return wrong permissions
     *
     * @return array
     */
    protected function getWrongPermissions()
    {
        if (!isset($this->wrongPermissions)) {
            $wrongEntries = array(
                'files' => array(),
                'dirs' => array(),
            );

            $common = $this->getCommonFolders();

            foreach ($this->getUpgradeEntries() as $entry) {
                foreach ($entry->getWrongPermissions() as $path) {
                    foreach ($common as $folder => $processed) {
                        if (false !== strpos($path, $folder)) {
                            if (\Includes\Utils\FileManager::isDir($path) && !$processed['dirs']) {
                                $this->wrongPermissions[] = 'find ' . $folder . ' -type d -execdir chmod 777 "{}" \\;;';
                                $common[$folder]['dirs'] = true;
                            } elseif (\Includes\Utils\FileManager::isFile($path) && !$processed['files']) {
                                $this->wrongPermissions[] = 'find ' . $folder . ' -type f -execdir chmod 666 "{}" \\;;';
                                $common[$folder]['files'] = true;
                            }
                            continue 2;
                        }
                    }
                    if (\Includes\Utils\FileManager::isDir($path)) {
                        $wrongEntries['dirs'][] = $path;

                    } else {
                        $wrongEntries['files'][] = $path;
                    }
                }
            }

            foreach ($wrongEntries as $type => $paths) {
                if ($paths) {
                    $permission = ($type == 'dirs') ? '777' : '666';
                    $this->wrongPermissions[] = 'chmod ' . $permission . ' ' . implode(' ', array_unique($paths)) . ';';
                }
            }
        }

        return $this->wrongPermissions;
    }

    /**
     * Return wrong permissions
     *
     * @return array
     */
    protected function getCommonFolders()
    {
        return array(
            rtrim(LC_DIR_CLASSES, '/') => false,
            rtrim(LC_DIR_SKINS, '/') => false,
            rtrim(LC_DIR_INCLUDES, '/') => false,
            LC_DIR_ROOT . 'sql' => false,
        );
    }

    /**
     * Return wrong permissions as string
     *
     * @return string
     */
    protected function getWrongPermissionsAsString()
    {
        $list = $this->getWrongPermissions();

        return $list ? implode('\\' . PHP_EOL, $list) : '';
    }
}
