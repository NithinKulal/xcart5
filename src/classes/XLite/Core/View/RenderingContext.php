<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

use XLite\Core\View\DTO\Assets;
use XLite\Core\View\DTO\RenderedWidget;

use XLite\Core\DependencyInjection\ContainerAwareTrait;
use XLite\Core\Templating\EngineInterface;
use XLite\Core\Templating\TemplateFinderInterface;

/**
 * {@inheritdoc}
 */
class RenderingContext implements RenderingContextInterface
{
    use ContainerAwareTrait;

    /** @var AssetRegistrarInterface */
    protected $assetRegistrar;

    /** @var MetaTagRegistrarInterface */
    protected $metaTagRegistrar;

    /**
     * Templating engine instance
     *
     * @var EngineInterface|TemplateFinderInterface
     */
    protected $templatingEngine;

    protected $bufferingLevel = 0;

    public function __construct(AssetRegistrarInterface $assetRegistrar, MetaTagRegistrarInterface $metaTagRegistrar)
    {
        $this->assetRegistrar   = $assetRegistrar;
        $this->metaTagRegistrar = $metaTagRegistrar;
    }

    /**
     * Returns a (cached) templating engine instance
     *
     * @return EngineInterface|TemplateFinderInterface
     */
    public function getTemplatingEngine()
    {
        return $this->getContainer()->make('templating_engine');
    }

    /**
     * {@inheritdoc}
     */
    public function registerAssets(Assets $assets)
    {
        $this->assetRegistrar->register($assets);
    }

    /**
     * {@inheritdoc}
     */
    public function registerMetaTags(array $tags)
    {
        $this->metaTagRegistrar->register($tags);
    }

    /**
     * {@inheritdoc}
     */
    public function startBuffering()
    {
        $this->bufferingLevel++;

        ob_start();

        $this->assetRegistrar->startBuffering();
        $this->metaTagRegistrar->startBuffering();
    }

    /**
     * {@inheritdoc}
     */
    public function stopBuffering()
    {
        $this->bufferingLevel--;

        $widget = new RenderedWidget(
            ob_get_contents(),
            $this->assetRegistrar->stopBuffering(),
            $this->metaTagRegistrar->stopBuffering()
        );

        ob_end_clean();

        return $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function isBuffering()
    {
        return $this->bufferingLevel > 0;
    }
}
