<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Tweet button
 *
 * @ListChild (list="buttons.share", weight="200")
 */
class Tweet extends \XLite\Module\CDev\GoSocial\View\Button\ASocialButton
{
    /**
     * Define button attributes
     *
     * @return array
     */
    protected function defineButtonParams()
    {
        $list = array();

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via) {
            $list['data-via'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via;
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_recommend) {
            $list['data-related'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_recommend;
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_hashtag) {
            $list['data-hashtags'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_hashtag;
        }

        return $list;
    }

    /**
     * The link caption that should be posted to the social networks. By default it’s the page’s title.
     *
     * @return string
     */
    protected function getDataTitle()
    {
        return $this->getTitle() ?: null;
    }
    
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_use;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonType()
    {
        return self::BUTTON_CLASS_TWITTER;
    }

    /**
     * Get button type
     *
     * @return string
     */
    function getButtonLabel()
    {
        return static::t('Tweet');
    }
}
