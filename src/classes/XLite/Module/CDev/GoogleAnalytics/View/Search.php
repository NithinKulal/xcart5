<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

use XLite\Module\CDev\GoogleAnalytics;

/**
 * Abstract widget
 */
abstract class Search extends \XLite\View\Form\Product\Search\Customer\Main implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (GoogleAnalytics\Main::useUniversalAnalytics()) {
            $list[] = 'modules/CDev/GoogleAnalytics/universal/action/ga-search.js';
        }

        return $list;
    }
}
