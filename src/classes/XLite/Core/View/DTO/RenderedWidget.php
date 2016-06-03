<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View\DTO;


/**
 * Widget data transfer object is used for storing serialized Widget representation (rendered content, assets) in cache.
 */
class RenderedWidget
{
    /** @var string */
    public $content;

    /** @var array[Assets] */
    public $assets;

    /** @var array */
    public $metaTags;

    public function __construct($content, array $assets, array $metaTags)
    {
        $this->content  = $content;
        $this->assets   = $assets;
        $this->metaTags = $metaTags;
    }
}