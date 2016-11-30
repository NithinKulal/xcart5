<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Controller\Admin;

/**
 * Logo and favicon changes
 *
 */
class LogoFavicon extends \XLite\Controller\Admin\Settings
{
    /**
     * Name of the configuration category
     */
    const LOGO_FAVICON_CATEGORY = 'CDev\SimpleCMS';

    /**
     * Page identification
     *
     * @var string
     */
    public $page = self::LOGO_FAVICON_CATEGORY;

    /**
     * Title of the page
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Logo & Favicon');
    }

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::LOGO_FAVICON_CATEGORY] = static::t('Logo & Favicon');

        return $list;
    }
}
