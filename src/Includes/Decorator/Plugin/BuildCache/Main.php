<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\BuildCache;

use Includes\Decorator\ClassBuilder\ClassBuilderFactory;
use Includes\Decorator\Utils\Operator;
use Includes\Utils\ModulesManager;

/**
 * Main
 */
class Main extends \Includes\Decorator\Plugin\APlugin
{

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Building class cache...';
    }

    /**
     * Execute certain hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        \Includes\Autoloader::switchToOriginalClassDir();

        $modules = array_keys(ModulesManager::processActiveModules());

        $classBuilder = (new ClassBuilderFactory())->create(LC_DIR_CLASSES, $this->getCacheClassesDir(), $modules);

        foreach (Operator::getClassFileIterator()->getIterator() as $file) {
            $classBuilder->buildPathname((string)$file);
        }
    }
}
