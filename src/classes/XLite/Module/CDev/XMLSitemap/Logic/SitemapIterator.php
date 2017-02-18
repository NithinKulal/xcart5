<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic;

use Includes\Utils\URLManager;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Router;

/**
 * Sitemap links iterator 
 */
class SitemapIterator extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Default priority 
     */
    const DEFAULT_PRIORITY = 0.5;

    /**
     * Product chunk size
     */
    const CHUNK_SIZE = 1000;

    /**
     * Position 
     * 
     * @var integer
     */
    protected $position = 0;

    /**
     * Categories length 
     * 
     * @var integer
     */
    protected $categoriesLength;

    /**
     * Products length 
     * 
     * @var integer
     */
    protected $productsLength;

    /**
     * Current chunk of products
     * 
     * @var array
     */
    protected $productsChunk;

    /**
     * Current chunk index
     * 
     * @var int
     */
    protected $currentChunkIndex = 0;

    /**
     * Language code
     *
     * @var string
     */
    protected $languageCode = null;

    /**
     * Is page has alternative language url
     *
     * @var boolean
     */
    protected $hasAlternateLangUrls;

    /**
     * Constructor
     *
     * @param string $languageCode Language code
     */
    public function __construct($languageCode = null)
    {
        $this->languageCode = $languageCode;
        $this->renewChunk(0);
        $this->currentChunkIndex = 0;
        parent::__construct();
    }

    /**
     * 
     */
    protected function renewChunk($position){
        if (isset($this->productsChunk)) {
            foreach ($this->productsChunk as $product) {                
                \XLite\Core\Database::getEM()->detach($product);
            }
        }

        $this->currentChunkIndex++;
        $this->productsChunk = \XLite\Core\Database::getRepo('XLite\Model\Product')
                                ->findAsSitemapLink($position, static::CHUNK_SIZE);
    }

    /**
     * Get current data
     * 
     * @return array
     */
    public function current()
    {
        // Refreshes counter as well
        set_time_limit(60);

        $data = null;

        if (0 == $this->position) {
            $data = $this->assembleWelcomeData();

        } elseif ($this->position < $this->getCategoriesLength() + 1) {

            $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->findOneAsSitemapLink($this->position - 1);

            if ($category && $category->isVisible()) {
                $data = $this->assembleCategoryData($category);
            }
            \XLite\Core\Database::getEM()->clear('XLite\Model\Category');

        } elseif ($this->position < $this->getCategoriesLength() + $this->getProductsLength() + 1) {
            $positionInProducts = $this->position - $this->getCategoriesLength() - 1;
            $positionInChunk    = $positionInProducts - static::CHUNK_SIZE * $this->currentChunkIndex;
            if ($positionInChunk >= static::CHUNK_SIZE) {
                $this->renewChunk($positionInProducts);
                $positionInChunk = 1;
            }
            $product = $this->productsChunk[$positionInChunk];
            if(!is_null($product)){
                $data = $this->assembleProductData($product);
            }
        }

        return $data;
    }

    /**
     * Get current key 
     * 
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Go to next record
     * 
     * @return void
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Rewind position
     * 
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
        $this->categoriesLength = null;
        $this->productsLength = null;
    }

    /**
     * Check current position
     * 
     * @return boolean
     */
    public function valid()
    {
        return $this->position < $this->count();
    }

    /**
     * Seek 
     * 
     * @param integer $position New position
     *  
     * @return void
     */
    public function seek($position)
    {
        $this->position = $position;
    }

    /**
     * Get length 
     * 
     * @return integer
     */
    public function count()
    {
        return $this->getCategoriesLength() + $this->getProductsLength() + 1;
    }

    /**
     * Get categories length 
     * 
     * @return integer
     */
    protected function getCategoriesLength()
    {
        if (!isset($this->categoriesLength)) {
            $this->categoriesLength = \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->countCategoriesAsSitemapsLinks();
        }

        return $this->categoriesLength;
    }

    /**
     * Get products length
     *
     * @return integer
     */
    protected function getProductsLength()
    {
        if (!isset($this->productsLength)) {
            $this->productsLength = \XLite\Core\Database::getRepo('XLite\Model\Product')
                ->countProductsAsSitemapsLinks();
        }

        return $this->productsLength;
    }

    /**
     * Check if store has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        if (null === $this->hasAlternateLangUrls) {
            $router = Router::getInstance();
            $this->hasAlternateLangUrls = LC_USE_CLEAN_URLS
                && $router->isUseLanguageUrls()
                && count($router->getActiveLanguagesCodes()) > 1;
        }

        return $this->hasAlternateLangUrls;
    }

    /**
     * Assemble welcome page data
     *
     * @return array
     */
    protected function assembleWelcomeData()
    {
        $result = [
            'loc' => \XLite::getInstance()->getShopURL(Converter::buildURL(\XLite::TARGET_DEFAULT, '', [], \XLite::getCustomerScript(), true)),
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->welcome_changefreq,
            'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->welcome_priority),
        ];

        if ($this->hasAlternateLangUrls()) {
            $url = Converter::buildURL(\XLite::TARGET_DEFAULT, '', [], \XLite::getCustomerScript(), true);

            if ($this->languageCode) {
                $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $url);
            }

            foreach (Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $code . '/' . $url;
                $locale = \XLite\Core\Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . URLManager::getShopURL($langUrl) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . URLManager::getShopURL($url) . '"';
            $result[$tag] = null;
        }

        return $result;
    }

    /**
     * Assemble category data 
     * 
     * @param \XLite\Model\Category $category Category
     *  
     * @return array
     */
    protected function assembleCategoryData(\XLite\Model\Category $category)
    {
        $_url = Converter::buildURL('category', '', ['category_id' => $category->getCategoryId()], \XLite::getCustomerScript(), true);
        $url = \XLite::getInstance()->getShopURL($_url);

        $result = [
            'loc' => $url,
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->category_changefreq,
            'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->category_changefreq),
        ];

        if ($this->hasAlternateLangUrls()) {
            if ($this->languageCode) {
                $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $_url);
            }

            foreach (Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $_url;
                $langUrl = $code . '/' . $langUrl;
                $locale = \XLite\Core\Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . URLManager::getShopURL($langUrl) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
            $result[$tag] = null;
        }

        return $result;
    }

    /**
     * Assemble product data 
     * 
     * @param \XLite\Model\Product $product Product
     *  
     * @return array
     */
    protected function assembleProductData(\XLite\Model\Product $product)
    {
        $_url = Converter::buildURL('product', '', ['product_id' => $product->getProductId()], \XLite::getCustomerScript(), true);
        $url = \XLite::getInstance()->getShopURL($_url);

        $result = [
            'loc' => $url,
            'lastmod' => Converter::time(),
            'changefreq' => Config::getInstance()->CDev->XMLSitemap->product_changefreq,
            'priority' => $this->processPriority(Config::getInstance()->CDev->XMLSitemap->product_priority),
        ];

        if ($this->hasAlternateLangUrls()) {
            if ($this->languageCode) {
                $result['loc'] = URLManager::getShopURL($this->languageCode . '/' . $_url);
            }

            foreach (Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $langUrl = $_url;
                $langUrl = $code . '/' . $langUrl;
                $locale = Converter::langToLocale($code);

                $tag = 'xhtml:link rel="alternate" hreflang="' . $locale . '" href="' . URLManager::getShopURL($langUrl) . '"';
                $result[$tag] = null;
            }

            $tag = 'xhtml:link rel="alternate" hreflang="x-default" href="' . $url . '"';
            $result[$tag] = null;
        }

        return $result;
    }

    /**
     * Process priority 
     * 
     * @param mixed $priority Link priority
     *  
     * @return string
     */
    protected function processPriority($priority)
    {
        $priority = is_numeric($priority) ? round(doubleval($priority), 1) : self::DEFAULT_PRIORITY;
        if (1 < $priority || 0 > $priority) {
            $priority = self::DEFAULT_PRIORITY;
        }

        return strval($priority);
    }
}
