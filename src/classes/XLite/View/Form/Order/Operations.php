<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Order;

/**
 * Operations form
 */
class Operations extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['order_id'] = $this->getOrder()->getOrderId();

        return $list;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return 'order-operations';
    }

}

