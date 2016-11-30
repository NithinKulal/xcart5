<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * HTTPS settings page widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class HttpsSettings extends \XLite\View\Dialog
{
    /**
     * Suffix of URL to check https availability
     */
    const CHECK_URI_SUFFIX = 'skins/common/js/php.js';

    /**
     * Flags
     */
    protected $isAvailableHTTPS;
    protected $isValidSSL;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'https_settings';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'https_settings';
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return $this->isCurlAvailable()
            ? parent::getBodyTemplate()
            : 'no_curl.twig';
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Get URL of the page where SSL certificate can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite::getXCartURL('http://www.x-cart.com/ssl');
    }

    /**
     * Get URL of the page where SSL certificate can be purchased
     *
     * @return string
     */
    protected function getReadMoreLink()
    {
        return 'https://www.sslshopper.com/ssl-checker.html';
    }

    /**
     * Get URL of the acticle about Inaccessible Admin area after enabling HTTPS
     *
     * @return string
     */
    protected function getArticleUrl()
    {
        return 'http://kb.x-cart.com/en/general_setup/inaccessible_admin_area_after_enabling_https.html';
    }

    /**
     * Check if curl is available and we can check availablilty of https
     *
     * @return boolean
     */
    protected function isCurlAvailable()
    {
        return function_exists('curl_init');
    }

    /**
     * Check if HTTPS feature is available and can be enabled
     *
     * @return boolean
     */
    protected function isAvailableHTTPS()
    {
        if (!isset($this->isAvailableHTTPS)) {
            $this->isAvailableHTTPS = \XLite\Core\URLManager::isSecureURLAccessible($this->getTestURL());
        }

        return $this->isAvailableHTTPS;
    }

    /**
     * Check if SSL certificate is valid
     *
     * @return boolean
     */
    protected function isValidSSL()
    {
        if (!isset($this->isValidSSL)) {
            $this->isValidSSL = \XLite\Core\URLManager::isSecureURLAccessible($this->getTestURL(), true);
        }

        return $this->isValidSSL;
    }

    /**
     * Get URL to test https connection
     *
     * @return string
     */
    protected function getTestURL()
    {
        return \XLite\Core\URLManager::getShopURL(static::CHECK_URI_SUFFIX, true);
    }

    /**
     * Get URL to test https connection
     *
     * @return string
     */
    protected function getDomain()
    {
        $url = parse_url($this->getTestURL());

        return $url['host'];
    }

    /**
     * Check if HTTPS options are enabled
     *
     * @return boolean
     */
    protected function isEnabledHTTPS()
    {
        return \XLite\Core\Config::getInstance()->Security->admin_security
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Retrun button style
     *
     * @return boolean
     */
    protected function getButtonStyle()
    {
        return $this->isAvailableHTTPS() && $this->isValidSSL()
            ? 'regular-main-button action'
            : 'inline';
    }

    /**
     * Buttons 'Enable HTTPS' and 'Disable HTTPS' are enabled
     *
     * @return boolean
     */
    protected function areButtonsEnabled()
    {
        return true;
    }
}
