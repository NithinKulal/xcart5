<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Model country selector controller
 */
class ModelCountrySelector extends \XLite\Controller\Admin\ModelSelector\AModelSelector
{
    const MAX_COUNTRY_COUNT = 10;

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param type $item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);
        $data['selected_value'] = $item->getCountry();

        return $data;
    }

    /**
     * Get data of the model request
     *
     * @return \Doctrine\ORM\PersistentCollection | array
     */
    protected function getData()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Country::P_SUBSTRING} = $this->getKey();
        $cnd->{\XLite\Model\Repo\Country::P_LIMIT}   = array(0, static::MAX_COUNTRY_COUNT);

        return \XLite\Core\Database::getRepo('XLite\Model\Country')->search($cnd);
    }

    /**
     * Format model text presentation
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function formatItem($item)
    {
        return $item->getCountry();
    }

    /**
     * Defines the model value
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function getItemValue($item)
    {
        return $item->getCode();
    }
}
