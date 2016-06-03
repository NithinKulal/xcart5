<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Core\View\DTO;


/**
 * Assets represent a collection of css and js assets with a specific "interface" and index (relative position).
 */
class Assets
{
    /**
     * An associative array with keys 'css' and 'js', values are arrays of either plain strings representing asset paths, or arrays of the following structure:
     * [
     *      'file'      => 'path/asset.css',
     *      'media'     => 'print'
     *      'filelist'  => [              // If you use this parameter then the 'file' parameter
     *                                    // is taken as a 'resource name',
     *          'path/javascript/js',     // and the real file paths must be provided via 'filelist' parameter
     *      ]
     *      'no_minify' => true,          // Skip minification
     * ]
     *
     * @var array
     */
    public $assets;

    /** @var int */
    public $index;

    /** @var string */
    public $interface;

    /** @var string */
    public $group;

    public function __construct($assets, $index, $interface, $group)
    {
        $this->assets    = $assets;
        $this->index     = $index;
        $this->interface = $interface;
        $this->group     = $group;
    }
}