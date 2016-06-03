<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Model product selector controller
 */
class ModelProductSelector extends \XLite\Controller\Admin\ModelSelector\AModelSelector
{
    /**
     * Products limit in the query
     */
    const MAX_PRODUCT_COUNT = 10;

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);
        $data['selected_value'] = $item->getName();
        $data['selected_sku']   = $item->getSKU();

        return $data;
    }

    /**
     * Get data of the model request
     *
     * @return \Doctrine\ORM\PersistentCollection | array
     */
    protected function getData()
    {
        $cnd = $this->getDataCondition();

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd);
    }

    /**
     * Returns data condition
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getDataCondition()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_SUBSTRING} = $this->getKey();
        $cnd->{\XLite\Model\Repo\Product::P_BY_TITLE}  = 'Y';
        $cnd->{\XLite\Model\Repo\Product::P_BY_SKU}    = 'Y';
        $cnd->{\XLite\Model\Repo\Product::P_LIMIT}     = array(0, static::MAX_PRODUCT_COUNT);
        $cnd->{\XLite\Model\Repo\Product::P_ORDER_BY}  = array('translations.name', 'asc');

        return $cnd;
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
        return $item->getSku() . ' - ' . $item->getName();
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
        return $item->getId();
    }
}
