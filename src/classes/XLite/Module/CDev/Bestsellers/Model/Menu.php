<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\Model;

/**
 * Menu
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class Menu extends \XLite\Module\CDev\SimpleCMS\Model\Menu implements \XLite\Base\IDecorator
{
    const DEFAULT_BESTSELLERS = '{bestsellers}';

    /**
     * Defines the resulting link values for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkURLs()
    {
        $list = parent::defineLinkURLs();
        $list[static::DEFAULT_BESTSELLERS] = '?target=bestsellers';

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
        $list[static::DEFAULT_BESTSELLERS] = 'XLite\Module\CDev\Bestsellers\Controller\Customer\Bestsellers';

        return $list;
    }
}
