<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\HTTP\Request;
use XLite\Core\URLManager;


/**
 * CloudSearch API client
 */
class ServiceApiClient extends \XLite\Base\Singleton
{
    /**
     * CloudSearch service access details
     */
    const CLOUD_SEARCH_DOMAIN                   = 'cloudsearch.x-cart.com';
    const CLOUD_SEARCH_REQUEST_SECRET_KEY_URL   = '/api/v1/getkey';
    const CLOUD_SEARCH_REMOTE_IFRAME_URL        = '/api/v1/iframe?key=';
    const CLOUD_SEARCH_REGISTER_URL             = '/api/v1/register';
    const CLOUD_SEARCH_SEARCH_URL               = '/api/v1/search';

    /**
     * Register CloudSearch installation
     *
     * @return void
     */
    public function register()
    {
        $requestUrl = 'http://' . static::CLOUD_SEARCH_DOMAIN . static::CLOUD_SEARCH_REGISTER_URL;

        $shopUrl = $this->getShopUrl();

        $request = new Request($requestUrl);
        $request->body = array(
            'shopUrl'   => $shopUrl,
            'shopType'  => 'xc5',
            'format'    => 'php',
        );

        $response = $request->sendRequest();

        if ($response && $response->code == 200) {
            $data = unserialize($response->body);

            if ($data && !empty($data['apiKey'])) {
                $this->storeApiKey($data['apiKey']);
            }
        }
    }

    /**
     * Search functionality on the product list
     * 
     * @param string $query Substring pattern for search
     * 
     * @return array
     */
    public function search($query)
    {
        $result = $this->getCachedSearch($query);

        if (!$result) {
            $result = $this->getSearchResult($query);

            $this->storeCachedSearch($query, $result);
        }

        return $result;
    }

    /**
     * Search result via search request
     * 
     * @param string $query
     * 
     * @return array
     */
    protected function getSearchResult($query)
    {
        $response = $this->makeSearchRequest($query);

        return ($response && $response->code === 200)
            ? $this->processSearchResponse($response)
            : null;
    }

    /**
     * Retrieve the product ids from the response body
     * 
     * @param \PEAR2\HTTP\Request\Response $response Response object
     * 
     * @return array
     */
    protected function processSearchResponse($response)
    {
        $result = json_decode($response->body, true);

        if (
            $result
            && $result['products']
            && count($result['products']) > 0
        ) {
            $result = array_map(function ($elem) {
                return intval($elem['id']);
            }, $result['products']);
        } else {
            $result = array();
        }

        return $result;
    }

    /**
     * Make product search request (ALL) into the CloudSearch service
     * 
     * @param string $query Query pattern
     * 
     * @return \PEAR2\HTTP\Request\Response
     */
    protected function makeSearchRequest($query)
    {
        $request = new Request(
            'http://' . static::CLOUD_SEARCH_DOMAIN
                . static::CLOUD_SEARCH_SEARCH_URL
        );

        $request->body = array(
            'apiKey'    => Config::getInstance()->QSL->CloudSearch->api_key,
            'q'         => $query,
            'all'       => 1,
        );

        return $request->sendRequest();
    }
    
    /**
     * Retrieve the cached search result if any
     * 
     * @param string $query Query pattern
     * 
     * @return array
     */
    protected function getCachedSearch($query)
    {
        return \XLite\Core\Database::getRepo('XLite\Module\QSL\CloudSearch\Model\SearchCache')
            ->getCachedSearch($query);
    }

    /**
     * Store the search result into the inner cache
     * 
     * @param string $query  Query pattern
     * @param array  $result Product ids array
     * 
     * @return void
     */
    protected function storeCachedSearch($query, $result)
    {
        \XLite\Core\Database::getRepo('XLite\Module\QSL\CloudSearch\Model\SearchCache')
            ->storeCachedSearch($query, $result);
    }

    /**
     * Ask CloudSearch server to send us a new secret key
     *
     * @return void
     */
    public function requestSecretKey()
    {
        $apiKey = Config::getInstance()->QSL->CloudSearch->api_key;

        $requestUrl = 'http://'
            . static::CLOUD_SEARCH_DOMAIN
            . static::CLOUD_SEARCH_REQUEST_SECRET_KEY_URL;

        $request = new Request($requestUrl);
        $request->body = array(
            'apiKey' => $apiKey,
        );

        $request->sendRequest();
    }

    /**
     * Get CloudSearch dashboard url
     *
     * @param $secretKey
     *
     * @return string
     */
    public function getDashboardIframeUrl($secretKey)
    {
        return 'https://'
            . static::CLOUD_SEARCH_DOMAIN
            . static::CLOUD_SEARCH_REMOTE_IFRAME_URL
            . $secretKey;
    }

    /**
     * Get store url without script part
     *
     * @return string
     */
    protected function getShopUrl()
    {
        return preg_replace(
            '/[^\/]*.php$/',
            '',
            URLManager::getShopURL(Converter::buildURL())
        );
    }

    /**
     * Store API key in the DB
     *
     * @param $key
     *
     * @return void
     */
    protected function storeApiKey($key)
    {
        $repo = Database::getRepo('XLite\Model\Config');

        $secretKeySetting = $repo->findOneBy(array(
            'name'      => 'api_key',
            'category'  => 'QSL\CloudSearch'
        ));

        $secretKeySetting->setValue($key);

        Database::getEM()->flush();
    }
}
