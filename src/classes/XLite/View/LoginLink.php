<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Login link
 */
class LoginLink extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/login.twig';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (
            !\XLite\Core\Config::getInstance()->Security->customer_security
            || $this->isHTTPS()
        ) {
            $list[] = 'js/login.js';
        }

        return $list;
    }

    /**
     * Get return url
     *
     * @return string|null
     */
    public function getReturnURLForData()
    {
        $result = null;

        if ($this->getTarget() === 'profile'
            && $this->isRegisterMode()
        ) {
            $result = $this->buildURL('order_list');
        }

        return $result;
    }

}

