<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\Model;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings implements \XLite\Base\IDecorator
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($option->getName() === 'nf_order_ttl') {
            $cell[static::SCHEMA_DEPENDENCY] = array(
                static::DEPENDENCY_SHOW => array(
                    'limit_nf_order_ttl' => array(true),
                ),
            );
        }

        return $cell;
    }
}