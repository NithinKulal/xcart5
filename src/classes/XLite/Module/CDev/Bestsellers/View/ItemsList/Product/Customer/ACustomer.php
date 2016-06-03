<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\View\ItemsList\Product\Customer;

/**
 * ACustomer
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->sortByModes += array(
            static::SORT_BY_MODE_BOUGHT => 'Sales sort',
        );
    }

    /**
     * Get products 'sort by' fields
     *
     * @return array
     */
    protected function getSortByFields()
    {
        $fields = parent::getSortByFields();
        $fields += array(
            'bought' => static::SORT_BY_MODE_BOUGHT,
        );

        return $fields;
    }
}
