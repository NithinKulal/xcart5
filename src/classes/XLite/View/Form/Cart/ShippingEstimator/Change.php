<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Cart\ShippingEstimator;

/**
 * Shipping estimator change shipping method form
 */
class Change extends \XLite\View\Form\Cart\ShippingEstimator\AShippingEstimator
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'change_method';
    }

    /**
     * Check and (if needed) set the return URL parameter
     *
     * @param array &$params Form params
     *
     * @return void
     */
    protected function setReturnURLParam(array &$params)
    {
        parent::setReturnURLParam($params);
        $params[\XLite\Controller\AController::RETURN_URL] = $this->buildFullURL('cart');
    }
}
