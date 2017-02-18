<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * Page step
 *
 * @Decorator\Depend ("CDev\XMLSitemap")
 */
class Page extends \XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\ASitemapStep
{
    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page');
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
        $pageId = isset($item['id']) ? $item['id'] : null;

        if ($pageId) {
            if (isset($item['cleanURL']) && static::isSitemapCleanUrlConditionApplicable()) {
                $_url = $item['cleanURL'];
            } else {
                $_url = Converter::buildURL('page', '', ['id' => $pageId], \XLite::getCustomerScript(), true);
            }
            $url = \XLite::getInstance()->getShopURL($_url);

            $result = [
                'loc' => $url,
                'lastmod' => Converter::time(),
                'changefreq' => Config::getInstance()->CDev->XMLSitemap->page_changefreq,
                'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->page_priority),
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