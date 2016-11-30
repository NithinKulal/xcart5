<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Access control
 *
 * @ListChild (list="center")
 */
class AccessControl extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'access_control';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'access_control/access_control.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'access_control/access_control.js';

        return $list;
    }

    /**
     * Add NOINDEX in meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = '<meta name="robots" content="noindex,nofollow"/>';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'access_control/access_control.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && (
            ($this->getAccessControlCell() && $this->getAccessControlCell()->isExpired()) 
            || $this->isAccessLocked()
        );
    }

    /**
     * Return default page title
     *
     * @return string
     */
    protected function getDefaultPageTitle()
    {
        return static::t('Access denied');
    }

    /**
     * Return url for action resend
     *
     * @return string
     */
    protected function getResendUrl()
    {
        return $this->buildURL('access_control', 'resend_link', ['key' => $this->getAccessControlCell()->getHash()]);
    }
}