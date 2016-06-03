<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DependencyInjection;

use DI\ContainerBuilder;
use XLite\Core\Layout;
use XLite\Core\Templating\TwigEngineFactory;

/**
 * Container factory instantiates DI container
 */
class ContainerFactory
{
    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function createContainer()
    {
        $builder = new ContainerBuilder();

        // TODO: setup definition caching (file system)

        $this->setupDefinitions($builder);

        return $builder->build();
    }

    /**
     * @return \Interop\Container\ContainerInterface
     */
    public function createDevContainer()
    {
        $builder = new ContainerBuilder();

        $this->setupDefinitions($builder);

        return $builder->build();
    }

    /**
     * Setup PHP-DI definitions.
     *
     * Note that autowiring is enabled by default. For example, DynamicWidgetRenderer's constructor arguments are automatically instantiated and injected.
     *
     * @param ContainerBuilder $builder
     */
    private function setupDefinitions(ContainerBuilder $builder)
    {
        $builder->addDefinitions([
            // Views
            'dynamic_widget_renderer'                 => \DI\object('XLite\Core\View\DynamicWidgetRenderer'),

            // Templating
            'templating_engine_factory'               => \DI\object('XLite\Core\Templating\TwigEngineFactory'),
            'templating_engine'                       => \DI\factory(['templating_engine_factory', 'getEngine']),
            'template_cache_manager'                  => \DI\object('XLite\Core\Templating\TwigCacheManager'),

            // X-Cart services
            'XLite\Core\Layout'                       => \DI\factory('XLite\Core\Layout::getInstance'),
            'layout'                                  => \DI\get('XLite\Core\Layout'),
            'widget_cache_manager'                    => \DI\object('XLite\Core\WidgetCacheManager'),
            'XLite\Core\WidgetCacheRegistryInterface' => \DI\get('widget_cache_manager'),
            'widget_cache'                            => \DI\object('XLite\Core\WidgetCache'),
            'XLite\Core\Cache\CacheKeyPartsGenerator'  => \DI\object('XLite\Core\Cache\CacheKeyPartsGenerator'),

            // Other services
            'event_dispatcher'                        => \DI\object('Symfony\Component\EventDispatcher\EventDispatcher'),
        ]);
    }

    /**
     * Get type mapping information to facilitate type hinting in IDEs.
     * Currently, only PHP Storm is supported via .dev/scripts/generate-phpstorm-metadata-file.php that generates .phpstorm.meta.php.
     * Arguably, there's no need to put all of the services defined in setupDefinitions in here.
     *
     * @return array
     */
    public static function getDefinitionTypeMappings()
    {
        return [
            // Views
            'dynamic_widget_renderer'   => 'XLite\Core\View\DynamicWidgetRenderer',

            // Templating
            'templating_engine_factory' => 'XLite\Core\Templating\TwigEngineFactory',
            'templating_engine'         => 'XLite\Core\Templating\TwigEngine',
            'template_cache_manager'    => 'XLite\Core\Templating\TwigCacheManager',

            // X-Cart services
            'layout'                    => 'XLite\Core\Layout',
            'widget_cache_manager'      => 'XLite\Core\WidgetCacheManager',
            'widget_cache'              => 'XLite\Core\WidgetCache',

            // Other services
            'event_dispatcher'          => 'Symfony\Component\EventDispatcher\EventDispatcher',
        ];
    }
}