<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Model;

/**
 * Product
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
class Category extends \XLite\Model\Category implements \XLite\Base\IDecorator
{
    public function getTags()
    {
        $config = \XLite\Core\Config::getInstance()->XC->ProductFilter;

        if ($config->attributes_filter_by_category) {
            $list = null;
            $languageCode = $this->getTranslationCode();

            if ($config->attributes_filter_cache_mode) {
                $data = $this->getTagsFromCache();

                if (isset($data[$languageCode])) {
                    $list = $data[$languageCode];
                }
            }

            if (null === $list) {
                $list = array();

                $tags = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')
                    ->findByCategory($this);

                foreach ($tags as $tag) {
                    $list[$tag->getId()] = $tag->getName();
                }

                if ($config->attributes_filter_cache_mode) {
                    $data[$languageCode] = $list;
                    $this->saveTagsFromCache($data);
                }
            }

        } else {
            $list = array();

            $tags = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tags')
                ->findByCategory($this);

            foreach ($tags as $tag) {
                $list[$tag->getId()] = $tag->getName();
            }
        }

        return $list;
    }

    /**
     * Get tags from cache
     *
     * @return mixed
     */
    protected function getTagsFromCache()
    {
        $key = 'ProductFilter_Category_Tags_' . $this->getCategoryId();

        return \XLite\Core\Database::getCacheDriver()->fetch($key);
    }

    /**
     * Save tags into the cache
     *
     * @param mixed   $data Data object for saving in the cache
     * @param integer $lifeTime Cell TTL OPTIONAL
     *
     * @return void
     */
    protected function saveTagsInCache($data, $lifeTime = 0)
    {
        $key = 'ProductFilter_Category_Tags_' . $this->getCategoryId();

        \XLite\Core\Database::getCacheDriver()->save($key, $data, $lifeTime);
    }
}
