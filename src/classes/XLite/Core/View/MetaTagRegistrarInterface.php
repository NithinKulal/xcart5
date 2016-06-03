<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View;


/**
 * Meta tag registrar allows widgets to register additional meta tags for the page.
 */
interface MetaTagRegistrarInterface
{
    /**
     * Register meta tags
     *
     * @param array $tags
     */
    public function register(array $tags);

    /**
     * Start buffering registered meta tags to be later retrieved by stopBuffering()
     */
    public function startBuffering();

    /**
     * Get all meta tags registered after the matching call to startBuffering()
     *
     * @return array
     */
    public function stopBuffering();
}