<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Templates\Plugin\Compiler;

use XLite\Core\DependencyInjection\ContainerAwareTrait;
use XLite\Core\Templating\CacheManagerInterface;

/**
 * Main
 *
 */
class Main extends \Includes\Decorator\Plugin\Templates\Plugin\APlugin
{
    use ContainerAwareTrait;

    /**
     * Execute "postprocess" hook handler
     *
     * @return void
     */
    public function executeHookHandler()
    {
        if (!LC_DEVELOPER_MODE) {
            $this->createTemplatesCache();
        }
    }

    /**
     * Static templates compilation
     *
     * @return void
     */
    protected function createTemplatesCache()
    {
        $cacheManager = $this->getTemplateCacheManager();

        foreach ($this->getAnnotatedTemplates() as $data) {
            try {
                $cacheManager->warmup($data['path']);
            } catch (\Exception $e) {
                // Ignore template compilation errors
            }
        }
    }

    /**
     * Returns a (cached) templating engine instance
     *
     * @return CacheManagerInterface
     */
    protected function getTemplateCacheManager()
    {
        return $this->getContainer()->get('template_cache_manager');
    }
}
