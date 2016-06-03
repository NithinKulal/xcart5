<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Payment\Method\Admin;

/**
 * Payment method settings form
 */
class Settings extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'payment_method';
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
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();
        $list['method_id'] = $this->getPaymentMethodId();

        if (\XLite\Core\Request::getInstance()->just_added) {
            $list['just_added'] = 1;
        }

        return $list;
    }

    /**
     * Get current zone Id
     *
     * @return integer
     */
    protected function getPaymentMethodId()
    {
        return \XLite\Core\Request::getInstance()->method_id;
    }
}
