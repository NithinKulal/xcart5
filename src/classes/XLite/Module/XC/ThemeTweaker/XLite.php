<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker;

/**
 * Class represents an order
 */
class XLite extends \XLite implements \XLite\Base\IDecorator
{
    /**
     * Initialize all active modules
     *
     * @return void
     */
    public function initModules()
    {
        parent::initModules();

        \XLite\Core\Layout::getInstance()->addSkin('theme_tweaker' . LC_DS . 'default', \XLite::CUSTOMER_INTERFACE);
    }
}
