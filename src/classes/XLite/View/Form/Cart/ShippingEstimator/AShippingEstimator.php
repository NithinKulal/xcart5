<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Cart\ShippingEstimator;

/**
 * Abstract shipping estimator form
 */
abstract class AShippingEstimator extends \XLite\View\Form\AForm
{
    /**
     * Get default form target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'shipping_estimate';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        return parent::getCommonFormParams() + array(
            'widget' => 'XLite\View\ShippingEstimator\ShippingEstimate',
        );
    }
}
