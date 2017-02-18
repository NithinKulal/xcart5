<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Logic;

use XLite\Core\Converter;
use XLite\Core\Config;

/**
 * Sitemap links iterator
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class SitemapIterator extends \XLite\Module\CDev\XMLSitemap\Logic\SitemapIterator implements \XLite\Base\IDecorator
{
    protected $newsLength;

    /**
     * Get current data
     *
     * @return array
     */
    public function current()
    {
        $data = parent::current();

        if (
            $this->position >= parent::count()
            && $this->position < (parent::count() + $this->getLength())
        ) {
            $data = \XLite\Core\Database::getRepo('XLite\Module\XC\News\Model\NewsMessage')
                ->findOneAsSitemapLink($this->position - parent::count(), 1);
            $data = $this->assembleNewsMessageData($data);
        }

        return $data;
    }

    /**
     * Get length
     *
     * @return integer
     */
    public function count()
    {
        return parent::count() + $this->getLength();
    }

    /**
     * Get pages length
     *
     * @return integer
     */
    protected function getLength()
    {
        if (!isset($this->newsLength)) {
            $this->newsLength = \XLite\Core\Database::getRepo('XLite\Module\XC\News\Model\NewsMessage')
                ->countAsSitemapsLinks();
        }

        return $this->newsLength;
    }

    /**
     * Assemble message data
     *
     * @param \XLite\Module\XC\News\Model\NewsMessage $newsMessage Message
     *
     * @return array
     */
    protected function assembleNewsMessageData(\XLite\Module\XC\News\Model\NewsMessage $newsMessage)
    {
        $_url = Converter::buildURL('news_message', '', ['id' => $newsMessage->getId()], \XLite::getCustomerScript(), false, true);
        $url = \XLite::getInstance()->getShopURL($_url);

        $result = [
            'loc' => $url,
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->news_changefreq,
            'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->news_priority),
        ];

        if ($this->hasAlternateLangUrls()) {
            if ($this->languageCode) {
                $result['loc'] = \Includes\Utils\URLManager::getShopURL($this->languageCode . '/' . $_url);
            }

            foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $_url;
                $langUrl = $code . '/' . $langUrl;
                $locale = Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . \Includes\Utils\URLManager::getShopURL($langUrl) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
            $result[$tag] = null;

        }
        return $result;
    }

}
