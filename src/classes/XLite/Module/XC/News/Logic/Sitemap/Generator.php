<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Logic\Sitemap;

/**
 * Generator
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class Generator extends \XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Generator implements \XLite\Base\IDecorator
{
    /**
     * Return steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        $list = parent::getStepsList();
        $list[] = 'XLite\Module\XC\News\Logic\Sitemap\Step\News';

        return $list;
    }
}
