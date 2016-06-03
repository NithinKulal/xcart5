<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Login\Customer;

/**
 * Abstract log-in form in customer interface
 */
abstract class ACustomer extends \XLite\View\Form\Login\ALogin
{
    /**
     * getSecuritySetting
     *
     * @return boolean
     */
    protected function getSecuritySetting()
    {
        return \XLite\Core\Request::getInstance()->isHTTPS()
            || \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        if (\XLite\Core\Request::getInstance()->popup && !\XLite\Core\Request::getInstance()->returnURL) {
            if (\XLite\Core\Request::getInstance()->fromURL) {
                $url = \XLite\Core\Request::getInstance()->fromURL;

            } else {
                $server = \XLite\Core\Request::getInstance()->getServerData();
                $url = empty($server['HTTP_REFERER']) ? null : $server['HTTP_REFERER'];
            }

            if ($url) {
                $list['popup'] = 1;
                $list['returnURL'] = $url;
            }
        } elseif ('checkout' === \XLite\Core\Request::getInstance()->target) {

            $list['returnURL'] = $this->buildURL('checkout');

        } else {
            $server = \XLite\Core\Request::getInstance()->getServerData();
            $url = empty($server['HTTP_REFERER']) ? null : $server['HTTP_REFERER'];

            $list['returnURL'] = $url;
        }

        return $list;
    }

    /**
     * getDefaultClassName
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' use-inline-error');
    }

}
