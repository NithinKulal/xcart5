<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UpdateInventory\Logic\Import;

/**
 * Importer
 */
class Importer extends \XLite\Logic\Import\Importer implements \XLite\Base\IDecorator
{
    /**
     * Add processor
     *
     * @return array
     */
    public static function getProcessorList()
    {
        return array_merge(
            parent::getProcessorList(),
            array(
                'XLite\Module\XC\UpdateInventory\Logic\Import\Processor\Inventory',
            )
        );
    }

    /**
     * Get list of steps to be excluded from update inventory routine
     *
     * @return array
     */
    public static function getExcludedImportSteps()
    {
        return array(
            'XLite\Logic\Import\Step\ImageResize',
        );
    }

    /**
     * Return true if current import is 'Update inventory'
     *
     * @return boolean
     */
    public function isUpdateInventory()
    {
        return \XLite\Module\XC\UpdateInventory\Main::TARGET_UPDATE_INVENTORY == $this->getOptions()->target;
    }

    /**
     * Filter steps before processing
     *
     * @return array
     */
    protected function processSteps()
    {
        if ($this->isUpdateInventory()) {
            $this->steps = array_filter(
                $this->steps,
                function($value) {
                    return !in_array($value, \XLite\Logic\Import\Importer::getExcludedImportSteps());
                }
            );
        }

        parent::processSteps();
    }

    /**
     * Preprocess import files
     *
     * @return void
     */
    protected function preprocessFiles()
    {
        parent::preprocessFiles();

        if ($this->isUpdateInventory()) {

            if ($this->getOptions()->linkedFiles) {
                // Process unarchived csv files
                foreach ($this->getOptions()->linkedFiles as $k => $list) {
                    $this->getOptions()->linkedFiles[$k] = $this->renameFiles(
                        $list,
                        \Includes\Utils\FileManager::getRealPath(LC_DIR_VAR . $this->getOptions()->dir) . LC_DS
                    );
                }

            } else {
                // Process csv files
                $this->getOptions()->files = $this->renameFiles($this->getOptions()->files);
            }
        }
    }

    /**
     * Rename files in the list or files to import
     *
     * @param array  $files List of files
     * @param string $dir   File directory OPTIONAL
     *
     * @return array
     */
    protected function renameFiles($files, $dir = '')
    {
        foreach ($files as $k => $path) {
            $pathParts = pathinfo($dir . $path);
            if (0 !== strpos(strtolower($pathParts['basename']), 'inventory')) {
                $newPath = $pathParts['dirname'] . LC_DS . 'inventory-' . $pathParts['basename'];
                \Includes\Utils\FileManager::move($dir . $path, $newPath);
                $files[$k] = $newPath;
            }
        }

        return $files;
    }
}
