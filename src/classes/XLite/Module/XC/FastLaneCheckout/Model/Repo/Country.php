<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Model\Repo;

/**
 * The "Country" model repository
 *
 */
class Country extends \XLite\Model\Repo\Country implements \XLite\Base\IDecorator
{
    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findAllLanguages();

        $codes = array_map(
            function($lng) {
                return $lng->getCode();
            },
            $languages
        );
        foreach ($codes as $code) {
            $list['enabled_dto_'.$code] = array(
                self::RELATION_CACHE_CELL => array(
                    '\XLite\Model\State',
                ),
            );
        }

        return $list;
    }

    /**
     * findAllEnabledDTO. Like findAllEnabled(), but with DTOs
     *
     * @return array
     */
    public function findAllEnabledDTO()
    {
        $cacheKey = 'enabled_dto_' . $this->getTranslationCode();

        $data = $this->getFromCache($cacheKey);

        if (!isset($data)) {
            $allEnabled = $this->findAllEnabled();

            $data = array_map(function ($item) {
                return [
                    "key"   => $item->getCode(),
                    "name"  => $item->getCountry()
                ];
            }, $allEnabled);
            $this->saveToCache($data, $cacheKey);
        }

        return $data;
    }
}
