<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Customer;

use XLite\Core\Request;
use XLite\Module\QSL\CloudSearch\Core\StoreApi;

/**
 * CloudSearch API controller
 */
class CloudSearchApi extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Stub for the CMS connectors
     *
     * @return boolean
     */
    protected function checkStorefrontAccessibility()
    {
        return true;
    }

    /**
     * 'info' api verb
     *
     * @return void
     */
    protected function doActionInfo()
    {
        $api = StoreApi::getInstance();

        $data = $api->getEntityCounts();

        $this->printOutputAndExit($data);
    }

    /**
     * 'products' api verb
     *
     * @return void
     */
    protected function doActionProducts()
    {
        $api = StoreApi::getInstance();

        list($start, $limit) = $this->getLimits();

        $data = $api->getProducts($start, $limit);

        $this->printOutputAndExit($data);
    }

    /**
     * 'categories' api verb
     *
     * @return void
     */
    protected function doActionCategories()
    {
        $api = StoreApi::getInstance();

        list($start, $limit) = $this->getLimits();

        $data = $api->getCategories($start, $limit);

        $this->printOutputAndExit($data);
    }

    /**
     * 'pages' api verb
     *
     * @return void
     */
    protected function doActionPages()
    {
        $api = StoreApi::getInstance();

        list($start, $limit) = $this->getLimits();

        $data = $api->getPages($start, $limit);

        $this->printOutputAndExit($data);
    }

    protected function doActionManufacturers()
    {
        $api = StoreApi::getInstance();

        $data = $api->getBrands();

        $this->printOutputAndExit($data);
    }

    protected function doActionGetPrices()
    {
        $prices = array();        
        $products = array();
        
        $currency = $this->getCart()->getCurrency();

        foreach($this->getProducts() as $product) {
            $products[$product->getProductId()] = \XLite\View\AView::formatPrice($product->getDisplayPrice(), $currency);
        }

        foreach($this->getProductIds() as $productId) {
            $prices[] = $products[$productId];
        }

        $this->printJSONAndExit($prices);
    }

    protected function getProducts()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->findByIds($this->getProductIds());
    }

    protected function getProductIds()
    {
        return explode(',', \XLite\Core\Request::getInstance()->ids);
    }

    /**
     * Stores new secret key sent from CloudSearch server
     *
     * @return void
     */
    protected function doActionSetSecretKey()
    {
        $api = StoreApi::getInstance();

        $data = $api->setSecretKey(
            Request::getInstance()->key,
            Request::getInstance()->signature
        );

        $this->printOutputAndExit($data);
    }

    /**
     * Render output and finish
     *
     * @param $output
     *
     * @return void
     */
    protected function printOutputAndExit($output)
    {
        header('Content-type: application/php');

        echo serialize($output);

        exit;
    }

    protected function printJSONAndExit($data)
    {
        header('Content-type: application/json');

        echo json_encode($data);

        exit;
    }

    /**
     * Get adjusted request limits
     *
     * @return array
     */
    protected function getLimits()
    {
        $request = Request::getInstance();

        $start = max(0, $request->start);
        $limit = max(1, $request->limit ?: StoreApi::MAX_ENTITIES_AT_ONCE);

        return array($start, $limit);
    }
}
