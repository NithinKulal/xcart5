<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\RSS;

/**
 * RSS Lazy load
 *
 * @ListChild (list="dashboard-sidebar", weight="400", zone="admin")
 */
class RSSLazyLoad extends \XLite\View\Base\ALazyLoad
{
    /**
     * Returns default lazy class
     *
     * @return string
     */
    protected function getDefaultLazyClass()
    {
        return 'XLite\View\RSS\RSS';
    }
}
