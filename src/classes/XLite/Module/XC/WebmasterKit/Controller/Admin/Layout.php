<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Controller\Admin;

/**
 * Layout
 */
class Layout extends \XLite\Controller\Admin\Layout implements \XLite\Base\IDecorator
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'switch_debug_bar';

        return $list;
    }

    /**
     * Switch state
     *
     * @return void
     */
    protected function doActionSwitchDebugBar()
    {
        $value = !\XLite\Core\Config::getInstance()->XC->WebmasterKit->debugBarEnabled;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\WebmasterKit',
                'name'     => 'debugBarEnabled',
                'value'    => $value,
            )
        );

        \XLite\Core\TopMessage::addInfo(
            $value
                ? 'DebugBar is enabled'
                : 'DebugBar is disabled'
        );

        $this->setReturnURL($this->buildURL('layout'));
    }
}
