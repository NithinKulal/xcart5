<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Model;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get SEO promo block URL
     *
     * @return string
     */
    protected function getSeoPromoURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/seo-consulting.html');
    }
}
