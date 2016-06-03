<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model;

/**
 * Menu
 *
 * @Decorator\Depend ("CDev\SimpleCMS")
 */
class Menu extends \XLite\Module\CDev\SimpleCMS\Model\Menu implements \XLite\Base\IDecorator
{
    /**
     * Defines the link controller class names for the specific link values
     * for example: {home}
     *
     * @return array
     */
    protected function defineLinkControllers()
    {
        $list = parent::defineLinkControllers();

        $list[static::DEFAULT_MY_ACCOUNT][] = '\XLite\Module\CDev\PINCodes\Controller\Customer\PinCodes';

        return $list;
    }
}
