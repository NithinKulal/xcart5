<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Shipping;

/**
 * Edit shipping rates form
 */
class Offline extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'shipping_rates';
    }

    /**
     * getDefaultAction
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
        $list['methodId'] = $this->getMethodId();
        $list['widget'] = 'XLite\View\Shipping\EditMethod';

        return $list;
    }

    /**
     * Get current method id
     *
     * @return integer
     */
    protected function getMethodId()
    {
        return \XLite\Core\Request::getInstance()->methodId;
    }

}
