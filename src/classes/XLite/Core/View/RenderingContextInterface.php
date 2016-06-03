<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

use XLite\Core\View\DTO\Assets;
use XLite\Core\View\DTO\RenderedWidget;

/**
 * Every widget has a rendering context which serves two purposes:
 * First, it provides a way to register widget output via interface methods registerAssets() and ... (html widget output is registered automatically from stdout).
 * Second, it has an output buffering mechanism that allows to pack all widget output in RenderedWidget data transfer object to facilitate caching.
 *
 * All widgets in a widget tree share the same rendering context, i.e. child widgets have the same rendering context instance as their parent. It allows parents to capture the output of their children using the buffering mechanism.
 */
interface RenderingContextInterface
{
    /**
     * Register widget assets (css and js).
     *
     * @param Assets $assets
     */
    public function registerAssets(Assets $assets);

    /**
     * Register an array of meta tags (in the form of ['<meta name="name1" content="Content1" />', ...])
     *
     * @param array $tags
     */
    public function registerMetaTags(array $tags);

    /**
     * Start buffering widget output (html and assets).
     */
    public function startBuffering();

    /**
     * Stop buffering widget output (html and assets) and return RenderedWidget that represents the output registered after the matching call to startBuffering().
     *
     * @return RenderedWidget
     */
    public function stopBuffering();

    /**
     * Return true if current context is buffering widget output (html and assets). Output buffering can be nested and this function returns true if output buffering level is greater than zero.
     *
     * @return mixed
     */
    public function isBuffering();
}