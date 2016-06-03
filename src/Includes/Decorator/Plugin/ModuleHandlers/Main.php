<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\ModuleHandlers;

/**
 * Main 
 *
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    protected $doneClasses = array();

    /**
     * Recursive run of runBuildCacheHandler with dependencies
     * 
     * @param string    $class  Class name of module to run cach handler
     * @param array     $stack  Depenencies stack of module                 OPTIONAL
     * 
     * @return void
     */
    protected function runBuildCacheHandler($class, $stack = array())
    {
        if (in_array($class, $this->doneClasses)) {
            return;
        }

        if (in_array($class, $stack)) {
            throw new Exception("A circular dependency found:" . $class  . '  ' . var_export($stack, true), 500);
        }

        $dependenciesClasses = array_map(
            function($name){
                return \Includes\Utils\ModulesManager::getClassNameByModuleName($name);
            }, $class::getDependencies()
        );

        $stack[] = $class;

        foreach ($dependenciesClasses as $depClass) {
            $this->runBuildCacheHandler($depClass, $stack);
        }

        $class::runBuildCacheHandler();
        $this->doneClasses[] = $class;
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        $modulesNames = array_keys(\Includes\Utils\ModulesManager::processActiveModules());

        $classes = array_map(function($name){
            return \Includes\Utils\ModulesManager::getClassNameByModuleName($name);
        }, $modulesNames);

        foreach ($classes as $class) {
            $this->runBuildCacheHandler($class);
        }
    }
}
