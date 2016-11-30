<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Cache management page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CacheManagement extends \XLite\View\SimpleDialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'cache_management';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'settings/cache_management.twig';
    }

    /**
     * Get last cache bebuild time war
     */
    public function getLastRebuildTimeRaw()
    {
        return \XLite\Core\Converter::convertTimeToUser(\XLite::getLastRebuildTimestamp());
    }

    /**
     * Get last cache bebuild time
     */
    public function getLastRebuildTime()
    {
        return \XLite\Core\Converter::formatTime($this->getLastRebuildTimeRaw(), null, false);
    }
}
