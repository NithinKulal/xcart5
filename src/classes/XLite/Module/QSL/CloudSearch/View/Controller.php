<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View;

use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Module\QSL\CloudSearch\Core\ServiceApiClient;
use XLite\Module\QSL\CloudSearch\View\CloudFilters\FiltersBox;
use XLite\Module\QSL\CloudSearch\View\CloudFilters\FiltersBoxPlaceholder;

/**
 * Controller widget extension
 */
class Controller extends \XLite\View\Controller implements \XLite\Base\IDecorator
{
    const CLOUD_SEARCH_MAX_PRODUCT_COUNT_IN_POPUP = 7;

    /**
     * @var bool
     */
    protected static $showCloudFilters = false;

    /**
     * @var CommonCell
     */
    protected static $cloudFilterConditions;

    /**
     * @var bool
     */
    protected static $isAsyncCloudFilters;

    /**
     * Return common data to send to JS
     *
     * @return array
     */
    protected function getCommonJSData()
    {
        $data = parent::getCommonJSData();

        if (!\XLite::isAdminZone()) {
            $data += $this->getCloudSearchInitData();
        }

        return $data;
    }

    /**
     * Get CloudSearch initialization data to pass to the JS code
     *
     * @return array
     */
    protected function getCloudSearchInitData()
    {
        $lng = array(
            'lbl_showing_results_for'  => static::t('cs_showing_results_for'),
            'lbl_see_details'          => static::t('cs_see_details'),
            'lbl_see_more_results_for' => static::t('cs_see_more_results_for'),
            'lbl_suggestions'          => static::t('cs_suggestions'),
            'lbl_products'             => static::t('cs_products'),
            'lbl_categories'           => static::t('cs_categories'),
            'lbl_pages'                => static::t('cs_pages'),
            'lbl_manufacturers'        => static::t('cs_manufacturers'),
            'lbl_did_you_mean'         => static::t('cs_did_you_mean'),
        );

        $membership = Auth::getInstance()->getMembershipId();

        $apiClient = new ServiceApiClient();

        $data = array(
            'cloudSearch' => array(
                'apiKey'               => $apiClient->getApiKey(),
                'priceTemplate'        => static::formatPrice(0),
                'selector'             => 'input[name="substring"]',
                'lng'                  => $lng,
                'membership'           => $membership,
                'dynamicPricesEnabled' => $this->isCloudSearchDynamicPricesEnabledCached(),
                'maxProducts'          => $this->getCloudSearchMaxProductCountInPopup(),
            ),
        );

        return $data;
    }

    /**
     * Replace FiltersBoxPlaceholder with the actual rendered FiltersBox
     *
     * @return void
     */
    protected function prepareContent()
    {
        parent::prepareContent();

        self::$bodyContent = preg_replace_callback(
            '/' . preg_quote(FiltersBoxPlaceholder::CLOUD_FILTERS_PLACEHOLDER_VALUE) . '/',
            function () {
                if (self::$showCloudFilters) {
                    $widget = $this->getChildWidget(
                        'XLite\Module\QSL\CloudSearch\View\CloudFilters\FiltersBox',
                        [
                            FiltersBox::PARAM_FILTER_CONDITIONS => self::$cloudFilterConditions,
                            FiltersBox::PARAM_IS_ASYNC_FILTERS  => self::$isAsyncCloudFilters,
                        ]
                    );

                    return $widget->getContent();
                } else {
                    return '';
                }
            },
            self::$bodyContent
        );
    }

    /**
     * Called from the outside to initialize FiltersBox
     *
     * @param $filterConditions
     * @param $isAsyncFilters
     */
    public static function showCloudFilters($filterConditions, $isAsyncFilters)
    {
        self::$showCloudFilters = true;

        self::$cloudFilterConditions = $filterConditions;

        self::$isAsyncCloudFilters = $isAsyncFilters;
    }

    /**
     * Enable dynamic prices if there are taxes configured
     *
     * @return bool
     */
    protected function isCloudSearchDynamicPricesEnabledCached()
    {
        $key = $this->getCloudSearchDynamicPricesEnabledCacheKey();

        $result = $this->getCache()->get($key);

        if ($result === null) {
            $result = $this->isCloudSearchDynamicPricesEnabled();

            $this->getCache()->set($key, $result);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getCloudSearchDynamicPricesEnabledCacheKey()
    {
        $key = [
            'XLite\Module\QSL\CloudSearch\View\Controller',
        ];

        $rateRepos = [
            'XLite\Module\CDev\VAT\Model\Tax\Rate',
            'XLite\Module\CDev\SalesTax\Model\Tax\Rate',
        ];

        foreach ($rateRepos as $rateRepo) {
            $repo = Database::getRepo($rateRepo);

            if ($repo !== null) {
                $key[] = $repo->getVersion();
            }
        }

        return $key;
    }

    /**
     * Enable dynamic prices if there are taxes configured
     *
     * @return bool
     */
    protected function isCloudSearchDynamicPricesEnabled()
    {
        $rateRepos = [
            'XLite\Module\CDev\VAT\Model\Tax\Rate',
            'XLite\Module\CDev\SalesTax\Model\Tax\Rate',
        ];

        foreach ($rateRepos as $rateRepo) {
            $repo = Database::getRepo($rateRepo);

            if ($repo !== null) {
                $rates = $repo->findAll();

                if (count($rates) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * A maximum number of products that could be displayed in a popup
     *
     * @return int
     */
    protected function getCloudSearchMaxProductCountInPopup()
    {
        return static::CLOUD_SEARCH_MAX_PRODUCT_COUNT_IN_POPUP;
    }
}
