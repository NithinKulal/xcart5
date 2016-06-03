<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Cart\ShippingEstimator;

/**
 * Open shipping estimator form
 */
class Open extends \XLite\View\Form\Cart\ShippingEstimator\AShippingEstimator
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return '';
    }

    /**
     * Get default form method
     *
     * @return string
     */
    protected function getDefaultFormMethod()
    {
        return 'get';
    }
}
