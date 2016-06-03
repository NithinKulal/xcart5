<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Repo;

/**
 * Clean URL repository
 */
class CleanURL extends \XLite\Model\Repo\CleanURL implements \XLite\Base\IDecorator
{
    const REVIEWS_PREFIX = 'reviews-';

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLProduct($url, $last = '', $rest = '', $ext = '')
    {
        $result = null;

        if ($ext) {
            $result = parent::parseURLProduct($url, $last, $rest, $ext);

            if (empty($result) && 0 === strpos($url, static::REVIEWS_PREFIX)) {
                $url = preg_replace('/^' . preg_quote(static::REVIEWS_PREFIX) . '/', '', $url);
                $result = parent::parseURLProduct($url, $last, $rest, $ext);

                if ($result) {
                    $result[0] = 'product_reviews';
                }
            }
        }

        return $result;
    }

    /**
     * Hook for modules
     *
     * @param string $url    Main part of a clean URL
     * @param string $last   First part before the "url"
     * @param string $rest   Part before the "url" and "last"
     * @param string $ext    Extension
     * @param string $target Target
     * @param array  $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        list($newTarget, $params) = parent::prepareParseURL(
            $url,
            $last,
            $rest,
            $ext,
            'product_reviews' == $target ? 'product' : $target,
            $params
        );

        return array('product_reviews' == $target ? $target : $newTarget, $params);
    }

    /**
     * Build product URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    protected function buildURLProductReviews($params)
    {
        list($urlParts, $params) = $this->buildURLProduct($params);

        if (!empty($urlParts)) {
            $urlParts[0] = static::REVIEWS_PREFIX . $urlParts[0];
        }

        return array($urlParts, $params);
    }

    /**
     * Hook for modules
     *
     * @param string $target   Target
     * @param array  $params   Params
     * @param array  $urlParts URL parts
     *
     * @return array
     */
    protected function prepareBuildURL($target, $params, $urlParts)
    {
        return parent::prepareBuildURL(
            'product_reviews' == $target ? 'product' : $target,
            $params,
            $urlParts
        );
    }
}
