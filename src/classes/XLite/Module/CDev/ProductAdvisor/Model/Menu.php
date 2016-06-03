<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Model;

/**
 * Menu
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class Menu extends \XLite\Module\CDev\SimpleCMS\Model\Menu implements \XLite\Base\IDecorator
{
    const DEFAULT_NEW_ARRIVALS = '{new arrivals}';
    const DEFAULT_COMING_SOON  = '{coming soon}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();

        $list[static::DEFAULT_NEW_ARRIVALS] = '?target=new_arrivals';
        $list[static::DEFAULT_COMING_SOON] = '?target=coming_soon';

        return $list;
    }

    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        $list = parent::defineLinkControllers();

        $list[static::DEFAULT_COMING_SOON] = '\XLite\Module\CDev\ProductAdvisor\Controller\Customer\ComingSoon';
        $list[static::DEFAULT_NEW_ARRIVALS] = '\XLite\Module\CDev\ProductAdvisor\Controller\Customer\NewArrivals';

        return $list;
    }
}
