<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2013 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
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
