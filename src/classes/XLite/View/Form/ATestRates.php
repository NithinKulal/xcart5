<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Test shipping rates form widget
 */
abstract class ATestRates extends \XLite\View\Form\AForm
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'test';
    }

    /**
     * Return form attributes
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $list = parent::getFormAttributes();
        $list['target'] = 'shipping_test';

        return $list;
    }
}
