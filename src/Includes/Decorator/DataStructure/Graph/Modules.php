<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\DataStructure\Graph;

/**
 * Modules 
 *
 */
class Modules extends \Includes\DataStructure\Graph
{
    /**
     * List of critical path legths for all child nodes
     *
     * @var array
     */
    protected $criticalPaths;

    // {{{ Getters and setters

    /**
     * Alias
     *
     * @return string
     */
    public function getActualName()
    {
        return $this->getKey();
    }

    /**
     * Return module dependencies list
     *
     * @return array
     */
    public function getDependencies()
    {
        return \Includes\Utils\ModulesManager::callModuleMethod($this->getActualName(), 'getDependencies');
    }

    /**
     * Return list of mutually exclusive modules
     *
     * @return array
     */
    public function getMutualModulesList()
    {
        return \Includes\Utils\ModulesManager::callModuleMethod($this->getActualName(), 'getMutualModulesList');
    }

    // }}}

    // {{{ Getters and setters

    /**
     * Method to get critical path length for a node
     *
     * @param string $module Module actual name
     *
     * @return integer
     */
    public function getCriticalPath($module)
    {
        if (!isset($this->criticalPaths)) {
            $this->criticalPaths = $this->calculateCriticalPathLengths();
        }

        return \Includes\Utils\ArrayManager::getIndex($this->criticalPaths, $module);
    }

    /**
     * Calculate critical path lengths
     *
     * @return void
     */
    protected function calculateCriticalPathLengths($length = 1)
    {
        $result = array();

        foreach ($this->getChildren() as $child) {

            // Critical path legth is equal to the current level
            $result[$child->getActualName()] = $length;

            // Recursive call for the next level nodes
            $result += $child->{__FUNCTION__}($length + 1);
        }

        return $result;
    }

    // }}}
}
