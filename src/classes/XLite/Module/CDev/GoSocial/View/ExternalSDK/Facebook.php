<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\ExternalSDK;

/**
 * Facebook SDK loader
 *
 * @ListChild (list="body", zone="customer", weight="999998")
 */
class Facebook extends \XLite\View\ExternalSDK\AExternalSDK
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'product';
        $list[] = 'page';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/sdk/facebook.twig';
    }

    /**
     * Get javascript SDK URL
     *
     * @return string
     */
    protected function getSDKUrl()
    {
        return '//connect.facebook.net/' . $this->getLocale()
            . '/all.js?t=' . microtime(true)
            . '#' . http_build_query($this->getQuery());
    }

    /**
     * Get locale
     *
     * @return string
     */
    protected function getLocale()
    {
        return 'en_US';
    }

    /**
     * Get SDK URL hash query
     *
     * @return array
     */
    protected function getQuery()
    {
        $query = array(
            'xfbml'  => '1',
        );

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id) {
            $query['appId'] = preg_replace('/\D/Ss', '', \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id);
        }

        return $query;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && (
                \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_use
                || \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_comments_use
            )
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id;
    }
}

