<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * Page step
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class News extends \XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\ASitemapStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Module\XC\News\Model\NewsMessage');
    }

    // }}}

    // {{{ Row processing

    /**
     * Process item
     *
     * @param mixed $item
     */
    protected function processItem($item)
    {
        $id = isset($item['id']) ? $item['id'] : null;

        if ($id) {
            if (isset($item['cleanURL']) && static::isSitemapCleanUrlConditionApplicable()) {
                $_url = $item['cleanURL'];
            } else {
                $_url = Converter::buildURL('news_message', '', ['id' => $id], \XLite::getCustomerScript(), false, true);
            }
            $url = \XLite::getInstance()->getShopURL($_url);

            $result = [
                'loc' => $url,
                'lastmod' => Converter::time(),
                'changefreq' => Config::getInstance()->CDev->XMLSitemap->news_changefreq,
                'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->news_priority),
            ];

            if ($this->generator->hasAlternateLangUrls()) {
                if ($this->languageCode) {
                    $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $_url);
                }

                foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                    $langUrl = $_url;
                    $langUrl = $code . '/' . $langUrl;
                    $locale = Converter::langToLocale($code);

                    $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . URLManager::getShopURL($langUrl) . '"';
                    $result[$tag] = null;
                }

                $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
                $result[$tag] = null;

            }

            $this->generator->addToRecord($result);
        }
    }

    // }}}

    /**
     * Check if simplified clean url building applicable
     *
     * @return bool
     */
    public static function isSitemapCleanUrlConditionApplicable()
    {
        return LC_USE_CLEAN_URLS;
    }
}
