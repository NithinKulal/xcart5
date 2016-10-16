<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
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
        $numProducts = $repo->search(new CommonCell(), $repo::SEARCH_MODE_COUNT);

        $catRepo       = Database::getRepo('XLite\Model\Category');
        $numCategories = $catRepo->search(new CommonCell(), $catRepo::SEARCH_MODE_COUNT);

        $pageRepo = Database::getRepo('\XLite\Module\CDev\SimpleCMS\Model\Page');
        $numPages = $pageRepo ? $pageRepo->search(new CommonCell(array(PageRepo::PARAM_ENABLED => true)), $pageRepo::SEARCH_MODE_COUNT) : 0;

        return array(
            'numProducts'      => $numProducts,
            'numCategories'    => $numCategories,
            'numManufacturers' => $this->getBrandsCount(),
            'numPages'         => $numPages,
            'productsAtOnce'   => $this->getMaxEntitiesAtOnce(),
        );
    }

    protected function getMaxEntitiesAtOnce()
    {
        return static::MAX_ENTITIES_AT_ONCE;
    }
    
    protected function getBrandsCount()
    {
        return 0;
    }
    
    /**
     * Get products data
     *
     * @param $start
     * @param $limit
     *
     * @return array
     */
    public function getBrands()
    {
        return [];
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
        $cnd                                       = new CommonCell;
        $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = array($start, $limit);

        return array_map(
            array($this, 'getIndexProductHash'), 
            Database::getRepo('XLite\Model\Product')->search($cnd, \XLite\Model\Repo\Product::SEARCH_MODE_ENTITIES)
        );
    }

    public function getIndexProductHash($product)
    {
        return array(
            'name'          => $product->getName(),
            'description'   => $product->getDescription(),
            'id'            => $product->getProductId(),
            'sku'           => $this->getSkuInfo($product),
            'price'         => $product->getDisplayPrice(),
            'url'           => $this->getUrlProductHash($product),
            'category'      => $this->getCategoryProductHash($product),
            'modifiers'     => $this->getModifiersProductHash($product),
        ) + $this->getImageInfoProductHash($product);
    }

    protected function getSkuInfo($product)
    {
        return $product->getSku();
    }

    protected function getModifiersProductHash($product)
    {
        $result = array();

        // Attributes retrieving could have overloading effect on the DB
        // Avoid it if you do not need it actually
        if (\XLite\Module\QSL\CloudSearch\Main::doIndexModifiers()) {
            foreach ($product->getAllAttributes() as $attribute) {
                $values = array();
                $avs = $attribute->getAttributeValue($product);

                if (is_array($avs)) {
                    foreach ($avs as $av) {
                        if ($av->asString()) {
                            $values[] = $av->asString();
                        }
                    }
                } elseif (is_string($avs)) {
                    $values[] = $avs;
                }

                $result[] = array(
                    'name' => $attribute->getName(),
                    'values' => $values,
                );
            }
        }

        $result[] = $this->getAdditionalProductInfo($product);
        
        return $result;
    }

    protected function getAdditionalProductInfo($product)
    {
        return array(
            'name' => '_meta_additional_',
            'values' => array(
                $product->getBriefDescription(),
                $product->getMetaTags(),
                $product->getMetaDesc(),
            ),
        );
    }

    protected function getCategoryProductHash($product)
    {
        $result = array();
        $catRepo = Database::getRepo('XLite\Model\Category');

        foreach ($product->getCategories() as $category) {
            $path = array();

            foreach ($catRepo->getCategoryPath($category->getCategoryId()) as $c) {
                $path[] = $c->getName();
            }

            $result = array_merge($result, $path);
        }

        return $result;
    }

    protected function getUrlProductHash($product)
    {
        return Converter::buildFullURL(
            'product', '', array('product_id' => $product->getProductId())
        );
    }

    protected function getImageInfoProductHash($product)
    {
        $result = array();

        if ($product->getImage()) {
            list(
                $result['image_width'], 
                $result['image_height'], 
                $result['image_src']
            ) = $product->getImage()->getResizedURL(static::MAX_THUMBNAIL_WIDTH, static::MAX_THUMBNAIL_HEIGHT);
        }

        return $result;
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
        $cnd->{\XLite\Model\Repo\Category::P_LIMIT} = array($start, $limit);
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
            $cnd                            = new CommonCell;
            $cnd->{PageRepo::P_LIMIT}       = array($start, $limit);
            $cnd->{PageRepo::PARAM_ENABLED} = true;
            $pages                          = $repo->search($cnd, false);

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
