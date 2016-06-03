<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Form;

/**
 * Paypal settings form
 */
class Settings extends \XLite\View\Form\AForm
{
    /**
     * Get default target field value
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'paypal_settings';
    }

    /**
     * Get default action field value
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
        $list['method_id'] = \XLite\Core\Request::getInstance()->method_id;

        return $list;
    }
}