<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Core;

/**
 * Profiler
 *
 * @Decorator\Depend ("XC\WebmasterKit")
 */
class Profiler extends \XLite\Module\XC\WebmasterKit\Core\Profiler implements \XLite\Base\IDecorator
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->disallowedTargets[] = 'sitemap';

        parent::__construct();
    }
}
