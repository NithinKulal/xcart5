<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Plugin\UpdateModules;

/**
 * Main 
 *
 */
class Main extends \Includes\Decorator\Plugin\Doctrine\Plugin\APlugin
{
    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        // To cache data
        \Includes\Utils\ModulesManager::processActiveModules();

        // Prepare flag to use it later for loading or not loading modules' yaml files
        $isModulesFileExists = \Includes\Utils\ModulesManager::isModulesFileExists();

        // Walk through the "XLite/Module" directory
        foreach ($this->getModuleMainFileIterator()->getIterator() as $path => $data) {
            $dir    = $path;
            $name   = basename($dir = dirname($dir));
            $author = basename($dir = dirname($dir));
            $class  = \Includes\Utils\ModulesManager::getClassNameByAuthorAndName($author, $name);

            if (!\Includes\Utils\Operator::checkIfClassExists($class)) {
                require_once ($path);
            }

            \Includes\Utils\ModulesManager::switchModule($author, $name, $isModulesFileExists);
        }

        \Includes\Utils\ModulesManager::removeFile();
    }

    /**
     * Get iterator for module files
     *
     * @return \Includes\Utils\FileFilter
     */
    protected function getModuleMainFileIterator()
    {
        return new \Includes\Utils\FileFilter(LC_DIR_MODULES, $this->getModulesPathPattern());
    }

    /**
     * Pattern to use for paths in "Module" directory
     *
     * @return string
     */
    protected function getModulesPathPattern()
    {
        return '|^' . preg_quote(LC_DIR_MODULES) . '(\w)+' . LC_DS_QUOTED . '(\w)+' . LC_DS_QUOTED . 'Main.php$|Si';
    }
}
