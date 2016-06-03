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

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\CommonCell;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Module\CDev\SimpleCMS\Model\Repo\Page as PageRepo;

/**
 * CloudSearch store-side API methods
 */
class StoreApi extends \XLite\Base\Singleton
{
    /*
     * Maximum number of entities to include in API call response
     */
    const MAX_ENTITIES_AT_ONCE = 300;

    /*
     * Maximum thumbnail width/height
     */
    const MAX_THUMBNAIL_WIDTH = 150;
    const MAX_THUMBNAIL_HEIGHT = 150;

    /**
     * Trusted request origin IPs
     *
     * @var array
     */
    protected $trustedRequestOrigins = array('78.46.67.123');

    /**
     * Get general store info - entity counts
     *
     * @return array
     */
    public function getEntityCounts()
    {
        $repo        = Database::getRepo('XLite\Model\Product');
        $numProducts = $repo->search(new CommonCell(), true);

        $catRepo       = Database::getRepo('XLite\Model\Category');
        $numCategories = $catRepo->search(new CommonCell(), true);

        $pageRepo = Database::getRepo('\XLite\Module\CDev\SimpleCMS\Model\Page');
        $numPages = $pageRepo ? $pageRepo->search(new CommonCell(), true) : 0;

        return array(
            'numProducts'      => $numProducts,
            'numCategories'    => $numCategories,
            'numManufacturers' => 0,
            'numPages'         => $numPages,
            'productsAtOnce'   => static::MAX_ENTITIES_AT_ONCE,
        );
    }

    /**
     * Get products data
     *
     * @param $start
     * @param $limit
     *
     * @return array
     */
    public function getProducts($start, $limit)
    {
        $repo    = Database::getRepo('XLite\Model\Product');
        $catRepo = Database::getRepo('XLite\Model\Category');

        $cnd                                       = new CommonCell;
        $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = array($start, $limit);
        $products                                  = $repo->search($cnd);

        $productsArray = array();

        foreach ($products as $product) {
            $productHash = array(
                'name'        => $product->getName(),
                'description' => $product->getDescription() ?: $product->getBriefDescription(),
                'id'          => $product->getProductId(),
                'sku'         => $product->getSku(),
                'price'       => $product->getDisplayPrice(),
            );

            $productHash['url'] = Converter::buildFullURL(
                'product',
                '',
                array('product_id' => $product->getProductId())
            );

            if ($product->getImage()) {
                list($productHash['image_width'], $productHash['image_height'], $productHash['image_src']) =
                    $product->getImage()->getResizedURL(static::MAX_THUMBNAIL_WIDTH, static::MAX_THUMBNAIL_HEIGHT);
            }

            $productHash['category'] = array();

            foreach ($product->getCategories() as $category) {
                $path = array();

                foreach ($catRepo->getCategoryPath($category->getCategoryId()) as $c) {
                    $path[] = $c->getName();
                }

                $productHash['category'] = array_merge($productHash['category'], $path);
            }

            $productHash['modifiers'] = array();

            foreach ($product->getAllAttributes() as $attribute) {
                $values = array();
                $avs    = $attribute->getAttributeValue($product);

                if (is_array($avs)) {
                    foreach ($avs as $av) {
                        if ($av->asString()) {
                            $values[] = $av->asString();
                        }
                    }

                } elseif (is_string($avs)) {
                    $values[] = $avs;
                }

                $productHash['modifiers'][] = array(
                    'name'   => $attribute->getName(),
                    'values' => $values,
                );
            }

            $productsArray[] = $productHash;
        }

        return $productsArray;
    }

    /**
     * Get categories data
     *
     * @param $start
     * @param $limit
     *
     * @return array
     */
    public function getCategories($start, $limit)
    {
        $repo = Database::getRepo('XLite\Model\Category');

        $cnd                                             = new CommonCell;
        $cnd->{\XLite\Model\Repo\Category::SEARCH_LIMIT} = array($start, $limit);
        $categories                                      = $repo->search($cnd, false);

        $categoriesArray = array();

        $rootCatId = Database::getRepo('XLite\Model\Category')->getRootCategoryId();

        foreach ($categories as $category) {

            $parentId = $category->getParentId() == $rootCatId ? 0 : $category->getParentId();

            $categoryHash = array(
                'id'          => $category->getCategoryId(),
                'name'        => $category->getName(),
                'description' => $category->getViewDescription(),
                'parent'      => $parentId,
            );

            if ($category->getImage()) {
                list($categoryHash['image_width'], $categoryHash['image_height'], $categoryHash['image_src']) =
                    $category->getImage()->getResizedURL(static::MAX_THUMBNAIL_WIDTH, static::MAX_THUMBNAIL_HEIGHT);
            }

            $categoryHash['url'] = Converter::buildFullURL(
                'category',
                '',
                array('category_id' => $category->getCategoryId())
            );

            $categoriesArray[] = $categoryHash;
        }

        return $categoriesArray;
    }

    /**
     * Get categories data
     *
     * @param $start
     * @param $limit
     *
     * @return array
     */
    public function getPages($start, $limit)
    {
        $repo = Database::getRepo('\XLite\Module\CDev\SimpleCMS\Model\Page');

        $pagesArray = array();

        if ($repo) {
            $cnd                           = new CommonCell;
            $cnd->{PageRepo::SEARCH_LIMIT} = array($start, $limit);
            $cnd->{PageRepo::PARAM_ENABLED} = true;
            $pages                         = $repo->search($cnd, false);

            foreach ($pages as $page) {
                $pageHash = array(
                    'id'      => $page->getid(),
                    'title'   => $page->getName(),
                    'content' => $page->getBody(),
                    'url'     => $page->getFrontURL(),
                );

                $pagesArray[] = $pageHash;
            }
        }

        return $pagesArray;
    }

    /**
     * Stores new secret key sent from CloudSearch server
     *
     * @param $key
     * @param $signature
     *
     * @return array
     */
    public function setSecretKey($key, $signature)
    {
        if ($key && $signature) {
            $signature = base64_decode($signature);

            if ($this->isServiceIpTrusted()
                || $this->isSignatureCorrect($key, $signature)
            ) {

                $repo = Database::getRepo('XLite\Model\Config');

                $secretKeySetting = $repo->findOneBy(array(
                    'name'     => 'secret_key',
                    'category' => 'QSL\CloudSearch'
                ));

                $secretKeySetting->setValue($key);

                Database::getEM()->flush();
            }
        }

        return array();
    }

    /**
     * Check the signature against our public key
     *
     * @param $data
     * @param $signature
     *
     * @return bool
     */
    protected function isSignatureCorrect($data, $signature)
    {
        $result = false;

        if (function_exists('openssl_get_publickey')) {
            $pubKeyId = openssl_get_publickey($this->getPublicKey());

            $result = openssl_verify($data, $signature, $pubKeyId) == 1;
        }

        return $result;
    }

    /**
     * Get public encryption key
     *
     * @return string
     */
    protected function getPublicKey()
    {
        return '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC+sJv3R+kKUl0okgi7HoN6sGcM
4Lyp4LMkMYqwD0hK618lJwydI5PRMj3+vmCxVZcnoiAM/8XwGmH24y2s7D2/8/co
K55PFPn6T0V5++5oyyObofPe08kDoW6Ft2+yNcshmg1Vd711Vd37LLXWsaWpfcjr
82cfYTelfejE4IO5NQIDAQAB
-----END PUBLIC KEY-----';
    }

    /**
     * Check if remote request origin has a trusted IP
     *
     * @return bool
     */
    protected function isServiceIpTrusted()
    {
        return in_array($_SERVER['REMOTE_ADDR'], $this->trustedRequestOrigins);
    }
}
