<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Order;

/**
 * Abstract product-based form
 */
abstract class AOrder extends \XLite\View\Form\AForm
{
    /**
     * getDefaultFormMethod
     *
     * @return string
     */
    protected function getDefaultFormMethod()
    {
        return 'post';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array('mode' => 'search');
    }
}
