<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Units & Formats
 */
class UnitsFormats extends \XLite\Controller\Admin\Settings
{
    /**
     * Page
     *
     * @var string
     */
    public $page = self::UNITS_PAGE;

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::UNITS_PAGE] = static::t('Localization');

        return $list;
    }
}
