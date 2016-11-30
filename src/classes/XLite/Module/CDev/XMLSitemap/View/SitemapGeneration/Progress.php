<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\View\SitemapGeneration;


use XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Generator;

/**
 * Sitemap generation Progress
 */
class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/XMLSitemap/sitemap_generation/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/XMLSitemap/sitemap_generation/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/XMLSitemap/sitemap_generation/progress.twig';
    }

    /**
     * Returns processing unit
     *
     * @return mixed
     */
    protected function getProcessor()
    {
        return Generator::getInstance();
    }
}