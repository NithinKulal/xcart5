<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;

use XLite\Core\View\DTO\Assets;


/**
 * Asset registrar allows widgets to register their css and js assets
 */
interface AssetRegistrarInterface
{
    /**
     * Register assets
     *
     * @param Assets $assets
     */
    public function register(Assets $assets);

    /**
     * Start buffering registered assets to be later retrieved by stopBuffering()
     */
    public function startBuffering();

    /**
     * Get all assets registered after the matching call to startBuffering()
     *
     * @return array[Assets]
     */
    public function stopBuffering();
}