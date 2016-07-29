<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Image
 */
class CommonImage extends \XLite\View\Image
{
    /**
     * Get properties
     *
     * @return void
     */
    public function getProperties()
    {
        $props = parent::getProperties();

        foreach (['width', 'height'] as $key) {
            if (isset($props[$key])) {
                unset($props[$key]);
            }
        }

        return $props;
    }
}