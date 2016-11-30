<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Clean URLs Router
 * TODO: Refactor Controllers, CleanURL repo, etc. move routing logic to router
 */
class Router extends \XLite\Base\Singleton
{
    /**
     * @var array
     */
    protected $activeLanguagesCodes = null;

    /**
     * Process \XLite\Core\Request data
     */
    public function processCleanUrls()
    {
        $request = $this->getRequest();

        if (LC_USE_CLEAN_URLS) {
            //if new .htaccess else old
            if (empty($request->rest) && empty($request->last) && empty($request->ext) && !empty($request->url)) {
                $this->processCleanUrlLanguage();

                preg_match(
                    '#^((([./_a-z0-9-]+)/)?([._a-z0-9-]+?)/)?([._a-z0-9-]+?)(/?)(\.([_a-z0-9-]+))?$#i',
                    $request->url,
                    $matches
                );

                $request->rest = isset($matches[3]) ? $matches[3] : null;
                $request->last = isset($matches[4]) ? $matches[4] : null;
                $request->url = isset($matches[5]) ? $matches[5] : null;
                $request->ext = isset($matches[7]) ? $matches[7] : null;
            } else {
                $this->processCleanUrlLanguage();
            }
        }
    }

    /**
     * Process \XLite\Core\Request, detect and set language
     */
    public function processCleanUrlLanguage()
    {
        if ($this->isUseLanguageUrls()) {
            $request = $this->getRequest();

            //if new .htaccess else old
            if (empty($request->rest) && empty($request->last) && empty($request->ext) && !empty($request->url)) {
                if (preg_match('#^([a-z]{2})(/|$)#i', $request->url, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->url = substr($request->url, 3);
                }
            } else {
                if (preg_match('#^([a-z]{2})(/|$)#i', $request->rest, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->last = substr($request->last, 3);
                } elseif (preg_match('#^([a-z]{2})(/|$)#i', $request->last, $matches) && in_array($matches[1], $this->getActiveLanguagesCodes())) {
                    $request->setLanguageCode($matches[1]);
                    $request->last = substr($request->last, 3);
                }
            }
        }
    }

    public function isUseLanguageUrls()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('clean_urls', 'use_language_url')) == 'Y';
    }

    /**
     * Return array of codes for currently active languages
     *
     * @return array
     */
    public function getActiveLanguagesCodes()
    {
        if (null === $this->activeLanguagesCodes) {
            $result = [];

            foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                $result[] = $language->getCode();
            }

            $this->activeLanguagesCodes = $result;
        }

        return $this->activeLanguagesCodes;
    }

    /**
     * Return request object
     *
     * @return \XLite\Core\Request
     */
    public function getRequest()
    {
        return \XLite\Core\Request::getInstance();
    }
}