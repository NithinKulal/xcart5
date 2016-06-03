<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Sitemap\Core;

/**
 * Layout
 */
class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Define the pages where first sidebar will be hidden.
     *
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        $list = parent::getSidebarFirstHiddenTargets();
        $list[] = 'map';

        return $list;
    }

}
