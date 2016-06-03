<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic;

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
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
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
                \XLite\Core\Database::getEM()->detach($category);
            }

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
     * Assemble welcome page data
     *
     * @return array
     */
    protected function assembleWelcomeData()
    {
        return array(
            'loc'        => array('target' => \XLite::TARGET_DEFAULT),
            'lastmod'    => \XLite\Core\Converter::time(),
            'changefreq' => \XLite\Core\Config::getInstance()->CDev->XMLSitemap->welcome_changefreq,
            'priority'   => $this->processPriority(\XLite\Core\Config::getInstance()->CDev->XMLSitemap->welcome_priority),
        );
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
        return array(
            'loc'        => array('target' => 'category', 'category_id' => $category->getCategoryId()),
            'lastmod'    => \XLite\Core\Converter::time(),
            'changefreq' => \XLite\Core\Config::getInstance()->CDev->XMLSitemap->category_changefreq,
            'priority'   => $this->processPriority(\XLite\Core\Config::getInstance()->CDev->XMLSitemap->category_priority),
        );
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
        return array(
            'loc'        => array('target' => 'product', 'product_id' => $product->getProductId()),
            'lastmod'    => \XLite\Core\Converter::time(),
            'changefreq' => \XLite\Core\Config::getInstance()->CDev->XMLSitemap->product_changefreq,
            'priority'   => $this->processPriority(\XLite\Core\Config::getInstance()->CDev->XMLSitemap->product_priority),
        );
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
