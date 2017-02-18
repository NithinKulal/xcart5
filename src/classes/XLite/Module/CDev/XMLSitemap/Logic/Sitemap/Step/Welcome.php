<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Router;

/**
 * Welcome step
 */
class Welcome extends AStep
{
    /**
     * Key
     * 
     * @var int
     */
    private $position = 0;

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        $result = [
            'loc' => Converter::buildFullURL(\XLite::TARGET_DEFAULT, '', [], \XLite::getCustomerScript(), true),
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->welcome_changefreq,
            'priority' => ASitemapStep::processPriority(Config::getInstance()->CDev->XMLSitemap->welcome_priority),
        ];

        if ($this->generator->hasAlternateLangUrls()) {
            $url = Converter::buildURL(\XLite::TARGET_DEFAULT, '', [], \XLite::getCustomerScript(), true);
            
            if ($this->languageCode) {
                $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $url);
            }
            
            foreach (Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $code . '/' . $url;
                $locale = Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . htmlentities(URLManager::getShopURL($langUrl)) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . htmlentities(URLManager::getShopURL($url)) . '"';
            $result[$tag] = null;
        }

        return [
            0 => $result
        ];
    }

    /**
     * Run step
     *
     * @return boolean
     */
    public function run()
    {
        $time = microtime(true);

        $this->generator->setInProgress(true);

        $this->generator->addToRecord($this->getCurrent());

        $this->generator->setInProgress(false);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->getItems()[$this->position]);
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * \SeekableIterator::current
     *
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->getItems()[$this->position];
    }

    /**
     * \SeekableIterator::next
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        return count($this->getItems());
    }

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     */
    public function seek($position)
    {
        if ($this->position !== $position) {
            if ($position < $this->count()) {
                $this->position = $position;
            }
        }
    }


}