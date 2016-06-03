<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\RSS;

/**
 * RSS
 */
class RSS extends \XLite\View\Dialog
{
    /**
     * Max count of feeds
     */
    const MAX_COUNT  = 3;

    /**
     * Feeds
     *
     * @var array
     */
    protected $feeds;

    /**
     * Add widget specific CSS file
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
     * Add widget specific JS file
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache TTL (seconds)
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return 1800;
    }

    /**
     * Return widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'rss';
    }

    /**
     * Return RSS feed Url
     *
     * @return string
     */
    protected function getRSSFeedUrl()
    {
        return 'http://feeds.feedburner.com/qtmsoft';
    }

    /**
     * Return RSS Url
     *
     * @return string
     */
    protected function getRSSUrl()
    {
        return \XLite::getInstallationLng() === 'ru'
            ? 'http://www.x-cart.ru/rss_x_cart_5.xml'
            : 'http://www.x-cart.com/rss_x_cart_5.xml';
    }

    /**
     * Return Blog Url
     *
     * @return string
     */
    protected function getBlogUrl()
    {
        return \XLite::getInstallationLng() === 'ru'
            ? 'http://www.x-cart.ru/blog'
            : 'http://blog.x-cart.com';
    }

    /**
     * Prepare feeds
     *
     * @param string $url Url
     *
     * @return array
     */
    protected function prepareFeeds($url)
    {
        $feed = simplexml_load_file($url);
        $result = array();
        if ($feed && $feed->channel->item) {
            foreach ($feed->channel->item as $story) {
                $result[] = array (
                    'title' => (string) $story->title,
                    'desc'  => (string) $story->description,
                    'link'  => $story->link
                        . (strpos($story->link, '?') === false ? '?' : '&')
                        . 'utm_source=xc5admin&utm_medium=link2blog&utm_campaign=xc5adminlink2blog',
                    'date'  => strtotime($story->pubDate),
                );

                if (static::MAX_COUNT <= count($result)) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Return feeds
     *
     * @return array
     */
    protected function getFeeds()
    {
        if (!isset($this->feeds)) {
            $this->feeds = $this->prepareFeeds(
                $this->getRSSUrl()
            );
        }

        return $this->feeds;
    }
}
