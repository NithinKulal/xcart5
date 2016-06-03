<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form;

/**
 * Select address form
 */
class SelectAddress extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'select_address';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'select';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = parent::getDefaultParams();

        $list['atype'] = \XLite\Core\Request::getInstance()->atype;
        $list['addressId'] = $this->getCurrentAddressId();

        return $list;
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
        $params[\XLite\Controller\AController::RETURN_URL] = $this->buildFullURL('checkout');
    }

}
