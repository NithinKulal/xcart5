<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Module\QSL\CloudSearch\Main;

/**
 * Layout manager
 *
 * @Decorator\Depend("XC\CrispWhiteSkin")
 */
class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        $targets = parent::getSidebarFirstHiddenTargets();

        if (Main::isCloudFiltersEnabled()) {
            $targets = array_diff($targets, ['search']);
        }

        return $targets;
    }

    /**
     * @return array
     */
    protected function getSidebarSecondHiddenTargets()
    {
        $targets = parent::getSidebarSecondHiddenTargets();

        if (Main::isCloudFiltersEnabled()) {
            $targets = array_diff($targets, ['search']);
        }

        return $targets;
    }
}
