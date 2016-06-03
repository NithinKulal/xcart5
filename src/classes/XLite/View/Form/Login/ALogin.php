<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Login;

/**
 * Abstract log-in form
 */
abstract class ALogin extends \XLite\View\Form\AForm
{
    /**
     * getSecuritySetting
     *
     * @return boolean
     */
    abstract protected function getSecuritySetting();


    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'login';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'login';
    }

    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        $url = $this->getReturnURL();
        if ($url) {
            $result['returnURL'] = $url;
        }

        return $result;
    }

    /**
     * Return value for the <form action="..." ...> attribute
     *
     * @return string
     */
    protected function getFormAction()
    {
        return $this->getShopURL(\XLite::getInstance()->getScript(), $this->getSecuritySetting());
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' login-form');
    }

}
