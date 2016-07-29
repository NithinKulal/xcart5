<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View;

/**
 * Abstract widget
 */
abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'modules/QSL/CloudSearch/style.css';
        }

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'modules/QSL/CloudSearch/loader.js';
            $list[] = 'modules/QSL/CloudSearch/init.js';
            $list[] = 'modules/QSL/CloudSearch/lib/handlebars.min.js';
            $list[] = 'modules/QSL/CloudSearch/lib/jquery.hoverIntent.min.js';
        }

        return $list;
    }
}
