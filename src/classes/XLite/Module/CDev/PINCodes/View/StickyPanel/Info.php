<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\StickyPanel;

/**
 * Order info sticky panel
 */
class Info extends \XLite\View\StickyPanel\ItemForm implements \XLite\Base\IDecorator
{
    /**
     * 'Save changes' button of the sticky panel must be visible anyway
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        if (
            'product' == \XLite\Core\Request::getInstance()->target
            && 'pin_codes' == \XLite\Core\Request::getInstance()->page
        ) {
            $class .= ' always-visible';
        }

        return $class;
    }
}
