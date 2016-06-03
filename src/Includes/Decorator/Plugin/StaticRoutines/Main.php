<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\StaticRoutines;

/**
 * Main 
 *
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{
    /**
     * Name of the so called "static constructor"
     */
    const STATIC_CONSTRUCTOR_METHOD = '__constructStatic';

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        static::getClassesTree()->walkThrough(array($this, 'addStaticConstructorCall'));
    }

    /**
     * Add static constructor calls
     * NOTE: method is public since it's used as a callback in external class
     *
     * @param \Includes\Decorator\DataStructure\Graph\Classes $node Current node
     *
     * @return void
     */
    public function addStaticConstructorCall(\Includes\Decorator\DataStructure\Graph\Classes $node)
    {
        if ($this->checkForStaticConstructor($node)) {
            $this->writeCallToSourceFile($node);
        }
    }

    /**
     * Check if node has the static constructor defined
     *
     * @param \Includes\Decorator\DataStructure\Graph\Classes $node Node to check
     *
     * @return boolean
     */
    protected function checkForStaticConstructor(\Includes\Decorator\DataStructure\Graph\Classes $node)
    {
        return $node->getReflection()->hasStaticConstructor;
    }

    /**
     * Modify class source
     *
     * @param \Includes\Decorator\DataStructure\Graph\Classes $node Current node
     *
     * @return void
     */
    protected function writeCallToSourceFile(\Includes\Decorator\DataStructure\Graph\Classes $node)
    {
        $path = \Includes\Decorator\ADecorator::getCacheClassesDir() . $node->getPath();

        $content  = \Includes\Utils\FileManager::read($path);
        $content .= PHP_EOL . '// Call static constructor' . PHP_EOL;
        $content .= '\\' . $node->getClass() . '::' . static::STATIC_CONSTRUCTOR_METHOD . '();';

        \Includes\Utils\FileManager::write($path, $content);
    }
}
