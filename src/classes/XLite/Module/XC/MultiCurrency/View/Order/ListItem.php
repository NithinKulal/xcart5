<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Order;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Orders search widget
 */
class ListItem extends \XLite\View\Order\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Format price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     *
     * @return string
     */
    protected function formatOrderPrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        if ($this->getOrder()->isMultiCurrencyOrder()) {
            $value = MultiCurrency::getInstance()->convertValueByRate(
                $value,
                $this->getOrder()->getSelectedMultiCurrencyRate()
            );

            $currency = $this->getOrder()->getSelectedMultiCurrency();
        }

        return static::formatPrice($value, $currency, $strictFormat, true);
    }
}
