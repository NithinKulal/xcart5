<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Model\Repo;

/**
 * The "State" model repository
 *
 */
class State extends \XLite\Model\Repo\State implements \XLite\Base\IDecorator
{
    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all_dto'] = array(
            self::RELATION_CACHE_CELL => array('\XLite\Model\Country'),
        );

        return $list;
    }

    /**
     * findAllStatesDTO. Like findAllStates(), but with DTOs
     *
     * @return array
     */
    public function findAllStatesDTO()
    {
        $cacheKey = 'all_dto';
        $data = $this->getFromCache($cacheKey);

        if (!isset($data)) {
            $states = $this->findAllStates();
            $data = array_map(function ($item) {
                return [
                    "key" => $item->getStateId(),
                    "name" => $item->getState()
                ];
            }, $states);
            $this->saveToCache($data, $cacheKey);
        }

        return $data;
    }
}
