<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Order;

/**
 * Currency selector form
 */
class CurrencySelector extends \XLite\View\Form\AForm
{
    /**
     * getDefaultFormMethod
     *
     * @return string
     */
    protected function getDefaultFormMethod()
    {
        return 'get';
    }

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

}
