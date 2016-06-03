<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Product search widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="100")
 */
class Search extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array(
            'search',
        );
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Products search');
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/search';
    }
}
