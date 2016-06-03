<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Form;

/**
 * Canada Post parcel settings form
 */
class CreateReturn extends \XLite\View\Form\AForm
{
    /**
     * Get default target field value
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'capost_returns';
    }

    /**
     * Get default action field value
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'create_return';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = parent::getDefaultParams();

        $params['order_id'] = $this->getOrder()->getOrderId(); // get order ID (from controller)

        return $params;
    }
}
