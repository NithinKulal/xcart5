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
class Tweet extends \XLite\View\AView
{
    /**
     * Allowed languages
     *
     * @var array
     */
    protected $languages = array('nl', 'en', 'fr', 'de', 'id', 'it', 'ja', 'ko', 'pt', 'ru', 'es', 'tr');

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/tweet.twig';
    }

    /**
     * Define button attributes
     *
     * @return array
     */
    protected function defineButtonAttributes()
    {
        $url = urlencode(\XLite::getInstance()->getShopURL($this->getURL()));
        $list = array(
            'url'      => $url,
            'counturl' => $url,
        );

        if (!\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_show_count) {
            $list['count'] = 'none';
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via) {
            $list['via'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_via;
        }

        if ($this->getTitle()) {
            $list['text'] = urlencode($this->getTitle());
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_recommend) {
            $list['related'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_recommend;
        }

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_hashtag) {
            $list['hashtags'] = \XLite\Core\Config::getInstance()->CDev->GoSocial->tweet_hashtag;
        }

        $language = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        $list['lang'] = in_array($language, $this->languages) ? $language : 'en';

        return $list;
    }

    /**
     * Get button attributes hash string
     *
     * @return string
     */
    protected function getButtonAttributes()
    {
        $result = array();
        foreach ($this->defineButtonAttributes() as $name => $value) {
            $result[] = $name . '=' . $value;
        }
        
        return implode('&amp;', $result);
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
}
