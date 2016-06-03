<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Form\Review\Customer;

/**
 * Add/edit review form
 */
class Review extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    const PARAM_ID              = 'id';
    const PARAM_PRODUCT_ID      = 'product_id';
    const PARAM_RETURN_TARGET   = 'return_target';

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'review';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'modify';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = array(
            self::PARAM_ID              => \XLite\Core\Request::getInstance()->id,
            self::PARAM_PRODUCT_ID      => \XLite\Core\Request::getInstance()->product_id,
            self::PARAM_RETURN_TARGET   => \XLite\Core\Request::getInstance()->return_target,
        );

        return $params;
    }
}
