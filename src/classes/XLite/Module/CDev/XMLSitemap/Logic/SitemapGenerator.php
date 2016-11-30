<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic;

/**
 * Sitemap generator 
 */
class SitemapGenerator extends \XLite\Base\Singleton
{
    const TTL                   = 86400;
    const LOCK_FILE_TTL         = 3600;
    const LOCK_FILE             = '.generationStartedLock';
    const RECORDS_CHUNK_SIZE    = 1000;

    /**
     * File index 
     * 
     * @var integer
     */
    protected $fileIndex;

    /**
     * Buffer array for records
     * 
     * @var string
     */
    protected $recordsChunk = '';

    /**
     * Empty file flag
     * 
     * @var boolean
     */
    protected $emptyFile = false;

    /**
     * Is page has alternative language url
     *
     * @var boolean
     */
    protected $hasAlternateLangUrls;

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getPrefix()
    {
        return 'tmp_';
    }

    /**
     * Get sitemap index 
     * 
     * @return string
     */
    public function getIndex()
    {
        $string = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>' . PHP_EOL
            . '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $list = glob(LC_DIR_DATA . 'xmlsitemap.*.xml');
        if ($list) {
            foreach ($list as $path) {
                $name = basename($path);
                $loc = \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL('sitemap', '', array('index' => substr($name, 11, -4)), \XLite::getCustomerScript())
                );
                $time = filemtime($path);
                $string .= '<sitemap>'
                    . '<loc>' . htmlentities($loc, ENT_COMPAT, 'UTF-8') . '</loc>'
                    . '<lastmod>' . date('Y-m-d', $time) . 'T' . date('H:i:s', $time) . 'Z</lastmod>'
                    . '</sitemap>';
            }
        }

        return $string . '</sitemapindex>';
    }

    /**
     * Get sitemap by index
     * 
     * @param integer $index Index
     *  
     * @return string
     */
    public function getSitemap($index)
    {
        $path = LC_DIR_DATA . 'xmlsitemap.' . $index . '.xml';

        return \Includes\Utils\FileManager::isExists($path) ? file_get_contents($path) : null;
    }

    /**
     * Check - sitemap empty or not
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return 0 == $this->getIterator()->count();
    }

    /**
     * Check - sitemaps files generated or not
     * 
     * @return boolean
     */
    public function isGenerated()
    {
        $list = glob(LC_DIR_DATA . 'xmlsitemap.*.xml');

        return $list && 0 < count($list);
    }

    /**
     * Check - sitemaps files generation started or not
     * 
     * @return boolean
     */
    public function isGenerationStarted()
    {
        $result = FALSE;
        $lockFilePath = LC_DIR_TMP . self::LOCK_FILE;
        if(file_exists($lockFilePath) && !$this->isObsoleteLock($lockFilePath)) {
            $result = TRUE;
        }
        return $result;
    }

    /**
     * Check - lock file is obsolete or not
     * 
     * @param integer $ttl TTL OPTIONAL
     *  
     * @return boolean
     */
    public function isObsoleteLock($lockFilePath)
    {
        $ttl = self::LOCK_FILE_TTL;        
        $time = filemtime($lockFilePath);
        return $time && $time + $ttl < \XLite\Core\Converter::time();
    }

    /**
     * Check - sitemap file is obsolete or not
     * 
     * @param integer $ttl TTL OPTIONAL
     *  
     * @return boolean
     */
    public function isObsolete($ttl = self::TTL)
    {
        $time = null;

        $list = glob(LC_DIR_DATA . 'xmlsitemap.*.xml');
        if ($list) {
            foreach ($list as $path) {
                $time = $time ? min($time, filemtime($path)) : filemtime($path);
            }
        }

        return $time && $time + $ttl < \XLite\Core\Converter::time();
    }

    // {{{ Generate sitemaps

    /**
     * Generate index file and sitemap files
     * 
     * @return void
     */
    public function generate()
    {
        $this->setGenerationStartedLock();
        $prefix = static::getPrefix();
        $this->clear($prefix);
        $this->generateSitemaps();
        $this->moveSitemaps($prefix);
        $this->unlockGeneration();
    }

    /**
     * Clear files directory
     *
     * @param string $prefix Files prefix
     *
     * @return void
     */
    public function clear($prefix = '')
    {
        $list = glob(LC_DIR_DATA . $prefix . 'xmlsitemap.*.xml');
        if ($list) {
            foreach ($list as $path) {
                \Includes\Utils\FileManager::deleteFile($path);
            }
        }
    }

    /**
     * Move sitemaps files
     *
     * @param string $prefix Prefix of source files
     *
     * @return void
     */
    public function moveSitemaps($prefix = '')
    {
        $list = glob(LC_DIR_DATA . $prefix . 'xmlsitemap.*.xml');
        if ($list) {
            $sep = preg_quote(LC_DS, '/');
            foreach ($list as $path) {
                $to = preg_replace('/^(.+' . $sep . ')' . preg_quote($prefix, '/') . '(xmlsitemap\..*\.xml)$/', '\\1\\2', $path);
                \Includes\Utils\FileManager::move($path, $to);
            }
        }
    }

    /**
     * Generate sitemap files
     *
     * @return void
     */
    protected function generateSitemaps()
    {
        $this->initializeWrite();
        $this->recordsChunk = '';
        $counter = 1;
        $this->getIterator();
        foreach ($this->getIterator() as $record) {
            if (!empty($record)) {
                $this->recordsChunk .= $this->assembleRecord($record);
            }
            if ($counter > static::RECORDS_CHUNK_SIZE) {                
                $this->writeRecord($this->recordsChunk);
                $this->recordsChunk = '';
                $counter = 0;
            }
            $counter++;
        }

        if ($this->hasAlternateLangUrls()) {
            foreach (\XLite\Core\Router::getInstance()->getActiveLanguagesCodes() as $code) {
                $counter = 1;
                foreach ($this->getIterator($code) as $record) {
                    if (!empty($record)) {
                        $this->recordsChunk .= $this->assembleRecord($record);
                    }
                    if ($counter > static::RECORDS_CHUNK_SIZE) {
                        $this->writeRecord($this->recordsChunk);
                        $this->recordsChunk = '';
                        $counter = 0;
                    }
                    $counter++;
                }
            }
        }
        if (!empty($this->recordsChunk)) {
            $this->writeRecord($this->recordsChunk);
            $this->recordsChunk = '';
        }
        $this->finalizeWrite();
    }

    /**
     * Check if store has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        if (null === $this->hasAlternateLangUrls) {
            $router = \XLite\Core\Router::getInstance();
            $this->hasAlternateLangUrls = LC_USE_CLEAN_URLS
                && $router->isUseLanguageUrls()
                && count($router->getActiveLanguagesCodes()) > 1;
        }

        return $this->hasAlternateLangUrls;
    }

    /**
     * Create lock file
     * 
     * @return void
     */
    protected function setGenerationStartedLock()
    {
        if(file_exists(LC_DIR_TMP . self::LOCK_FILE)) {           
            \XLite\Logger::getInstance()->log('Previous XML-sitemap generation died without resetting lock file');
        }
        file_put_contents(LC_DIR_TMP . self::LOCK_FILE, '');
    }

    /**
     * Remove lock file
     * 
     * @return void
     */
    protected function unlockGeneration()
    {
        unlink(LC_DIR_TMP . self::LOCK_FILE);
    }

    /**
     * Get head 
     * 
     * @return string
     */
    protected function getHead()
    {
        return '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;
    }

    /**
     * Get footer 
     * 
     * @return string
     */
    protected function getFooter()
    {
        return '</urlset>';
    }

    /**
     * Assemble record 
     * 
     * @param array $record Record
     *  
     * @return string
     */
    protected function assembleRecord(array $record)
    {
        $time = $record['lastmod'];
        $record['lastmod'] = date('Y-m-d', $time) . 'T' . date('H:i:s', $time) . 'Z';

        $string = '<url>';
        foreach ($record as $tag => $value) {
            if (!empty($value)) {
                $string .= '<' . $tag . '>' . htmlentities($value) . '</' . $tag . '>';
            } else {
                $string .= '<' . $tag . ' />';
            }
        }

        return $string . '</url>';
    }

    /**
     * Build location URL
     * 
     * @param array $loc Locationb as array
     *  
     * @return string
     */
    protected function buildLoc(array $loc)
    {
        $target = $loc['target'];
        unset($loc['target']);

        return \XLite\Core\Converter::buildURL($target, '', $loc, \XLite::getCustomerScript(), true);
    }

    /**
     * Get iterator 
     *
     * @param string $languageCode Language code
     *
     * @return \XLite\Module\CDev\XMLSitemap\Logic\SitemapIterator
     */
    protected function getIterator($languageCode = null)
    {
        return new \XLite\Module\CDev\XMLSitemap\Logic\SitemapIterator($languageCode);
    }

    /**
     * Initialize write 
     * 
     * @return void
     */
    protected function initializeWrite()
    {
        if (!\Includes\Utils\FileManager::isExists(LC_DIR_DATA)) {
            \Includes\Utils\FileManager::mkdir(LC_DIR_DATA);
            if (!\Includes\Utils\FileManager::isExists(LC_DIR_DATA)) {
                \XLite\Logger::getInstance()->log(
                    'The directory ' . LC_DIR_DATA . ' can not be created.'
                    . ' Check the permissions to create directories.',
                    LOG_ERR
                );
            }
        }
        $this->fileIndex = null;
        $this->emptyFile = true;
    }

    /**
     * Finalize write 
     * 
     * @return void
     */
    protected function finalizeWrite()
    {
        if ($this->emptyFile) {
            if ($this->fileIndex) {
                \Includes\Utils\FileManager::deleteFile($this->getSitemapPath());
            }

        } else {
            \Includes\Utils\FileManager::write($this->getSitemapPath(), $this->getFooter(), FILE_APPEND);
        }
    }

    /**
     * Write record 
     * 
     * @param string $string String
     *  
     * @return void
     */
    protected function writeRecord($string)
    {
        if (!isset($this->fileIndex)) {
            $this->fileIndex = 1;
            \Includes\Utils\FileManager::write($this->getSitemapPath(), $this->getHead());
        }

        \Includes\Utils\FileManager::write($this->getSitemapPath(), $string, FILE_APPEND);
        $this->emptyFile = false;

        if ($this->needSwitch()) {
            \Includes\Utils\FileManager::write($this->getSitemapPath(), $this->getFooter(), FILE_APPEND);
            $this->fileIndex++;
            \Includes\Utils\FileManager::write($this->getSitemapPath(), $this->getHead(), FILE_APPEND);
            $this->emptyFile = true;
        }
    }

    /**
     * Get sitemap path
     * 
     * @return string
     */
    protected function getSitemapPath()
    {
        return LC_DIR_DATA . static::getPrefix() . 'xmlsitemap.' . $this->fileIndex . '.xml';
    }

    /**
     * Check - need switch to next file or not
     * 
     * @return boolean
     */
    protected function needSwitch()
    {
        return 5242880 < filesize($this->getSitemapPath());
    }

    // }}}
}

