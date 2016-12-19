<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\View\Tabs;

/**
 * Tabs related to seo settings
 */
abstract class SeoSettings extends \XLite\View\Tabs\SeoSettings implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return array_merge(
            parent::defineTabs(),
            [
                'sitemap' => [
                    'weight'   => 200,
                    'title'    => static::t('XML sitemap'),
                    'widget'   => 'XLite\Module\CDev\XMLSitemap\View\Admin\Sitemap',
                ],
            ]
        );
    }
}
