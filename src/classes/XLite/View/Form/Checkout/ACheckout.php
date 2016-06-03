<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Checkout abstract form
 */
abstract class ACheckout extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'checkout';
    }

    /**
     * Return form attributes
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $list = parent::getFormAttributes();

        if (!isset($list['class'])) {
            $list['class'] = '';
        }

        $list['class'] .= ' use-inline-error';
        $list['class'] = trim($list['class']);

        return $list;
    }

}
