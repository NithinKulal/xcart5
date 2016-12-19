<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to seo settings
 */
class SeoSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'settings' => [
                'weight'     => 100,
                'title'      => static::t('General'),
                'template'   => 'settings/clean_url/body.twig',
                'url_params' => ['page' => 'CleanURL']
            ]
        ];
    }
}
