<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;


abstract class ABackendAction
{
    /**
     * @return array
     */
    public function getCommonDataForBackend()
    {
        $result = [];

        $result['v']    = 1;
        $result['t']    = 'event';
        $result['ni']   = 1;
        $result['ec']   = 'Admin area changes';
        $result['ea']   = 'Backend action';
        $result['tid']  = \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account;
        $result['cid']  = $this->getCid();

        $result['dh'] = $_SERVER['HTTP_HOST'];
        $result['dp'] = $_SERVER['REQUEST_URI'];
        $result['dt'] = 'Backend';

        return $result;
    }

    /**
     * @return string
     */
    protected function getCid()
    {
        if(!\XLite\Core\Session::getInstance()->ga_uuid) {
            \XLite\Core\Session::getInstance()->ga_uuid = uniqid();
        }

        return \XLite\Core\Session::getInstance()->ga_uuid;
    }
}