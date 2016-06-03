<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin\Base;

/**
 * CustomJavaScript controller
 */
abstract class ThemeTweaker extends \XLite\Controller\Admin\AAdmin
{
    /**
     * FIXME- backward compatibility
     *
     * @var   array
     */
    protected $params = array('target');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Look & Feel');
    }

    /**
     * Get file content
     *
     * @return string
     */
    public function getFileContent()
    {
        return \Includes\Utils\FileManager::read($this->getFileName());
    }

    /**
     * Get backup name
     *
     * @return string
     */
    public function getBackupName()
    {
        return 'backup_' . \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Get backup content
     *
     * @return string
     */
    public function getBackupContent()
    {
        return \XLite\Core\Config::getInstance()->XC->ThemeTweaker->{$this->getBackupName()};
    }

    /**
     * Get file name
     *
     * @return string
     */
    protected function getFileName()
    {
        return \XLite\Module\XC\ThemeTweaker\Main::getThemeDir()
                . str_replace('_', '.', \XLite\Core\Request::getInstance()->target);
    }

    /**
     * Restore from backup
     *
     * @return void
     */
    protected function doActionRestore()
    {
        $this->saveCode($this->getBackupContent());
    }

    /**
     * Save
     *
     * @return void
     */
    protected function doActionSave()
    {
        $setting = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
            array(
                'name' => 'use_' . \XLite\Core\Request::getInstance()->target,
                'category' => 'XC\\ThemeTweaker'
            )
        );

        \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
            $setting,
            array('value' => isset(\XLite\Core\Request::getInstance()->use))
        );

        $data = \XLite\Core\Request::getInstance()->getNonFilteredData();
        $this->saveCode($data['code']);
    }

    /**
     * Save
     *
     * @param string $code Code
     *
     * @return void
     */
    protected function saveCode($code)
    {
        if ("\r\n" != PHP_EOL) {
            $code = str_replace("\r\n", PHP_EOL, $code);
        }
        $code = str_replace(chr(194) . chr(160), ' ', $code);
        $file = $this->getFileName();

        \Includes\Utils\FileManager::write($file, $code);

        if (\Includes\Utils\FileManager::isFileWriteable($file)) {
            \XLite\Core\TopMessage::addInfo('Your custom file is successfully saved');

            \Includes\Utils\FileManager::deleteFile(str_replace('custom', 'custom.min', $file));

            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'name'     => $this->getBackupName(),
                    'value'    => $code,
                    'category' => 'XC\\ThemeTweaker',
                )
            );
            \XLite\Core\Config::updateInstance();

            $config = \XLite\Core\Config::getInstance()->Performance;

            if (
                $config->aggregate_css
                || $config->aggregate_js
            ) {
                \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_CACHE_RESOURCES);
                \XLite\Core\TopMessage::addInfo('Aggregation cache has been cleaned');
            }

        } else {
            \XLite\Core\TopMessage::addError(
                'The file {{file}} does not exist or is not writable.',
                array(
                    'file' => $file
                )
            );
        }
    }
}
