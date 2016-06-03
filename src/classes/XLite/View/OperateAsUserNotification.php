<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Address book
 *
 * @ListChild (list="layout.main", zone="customer", weight="0")
 */
class OperateAsUserNotification extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'operate_as_user/body.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'operate_as_user/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'operate_as_user/common.js';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isOperatingAsUserMode();
    }

    /**
     * Get profile
     * 
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Auth::getInstance()->getProfile();
    }

    /**
     * Get profile name
     * 
     * @return string
     */
    protected function getName()
    {
        return $this->getProfile()->getName() !== static::t('n/a')
             ? $this->getProfile()->getName() . ', '
             : ' ';
    }

    /**
     * Get profile login
     * 
     * @return string
     */
    protected function getLogin()
    {
        return $this->getProfile()->getLogin();
    }

    /**
     * Get finishOperateAs action url
     * 
     * @return string
     */
    protected function getFinishOperateAsUrl()
    {
        return $this->buildURL('login', 'logoff');
    }
}

