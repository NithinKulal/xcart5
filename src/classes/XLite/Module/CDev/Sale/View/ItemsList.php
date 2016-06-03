<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * ItemsList
 */
abstract class ItemsList extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        return $this->getOnlyEntities(parent::getData($cnd, $countOnly));
    }

    /**
     * getPageData
     *
     * @return array
     */
    protected function getPageData()
    {
        return $this->getOnlyEntities(parent::getPageData());
    }

    /**
     * Return collection result from the mixed one.
     *
     * @param mixed $data Data
     *
     * @return mixed
     */
    protected function getOnlyEntities($data)
    {
        $result = $data;
        if (is_array($data)) {
            // Sanitize result array as it is contains the following values: array(0 => Product object, 'cnt' => <counter>)
            // We should return array of product objects
            $result = array();
            foreach ($data as $row) {
                $result[] = is_array($row) ? $row[0] : $row;
            }
        }
        return $result;
    }
}
