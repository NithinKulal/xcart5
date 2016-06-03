<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Misceleneous conversion routines
 */
class Converter extends \XLite\Core\Converter implements \XLite\Base\IDecorator
{
    /**
     * Selected multi currency
     *
     * @var \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     */
    protected $selectedMultiCurrency = null;

    /**
     * Convert price to selected currency according to rate
     *
     * @param float $value Value
     *
     * @return float
     */
    public function convertToSelectedMultiCurrency($value)
    {
        if (
            MultiCurrency::getInstance()->hasMultipleCurrencies()
            && !$this->getSelectedMultiCurrency()->isDefaultCurrency()
        ) {
            $value = MultiCurrency::getInstance()->convertValueByRate(
                $value,
                $this->getSelectedMultiCurrency()->getRate()
            );
        }

        return $value;
    }

    /**
     * Get selected currency
     *
     * @return \XLite\Module\XC\MultiCurrency\Model\ActiveCurrency
     */
    protected function getSelectedMultiCurrency()
    {
        if (is_null($this->selectedMultiCurrency)) {
            $this->selectedMultiCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();
        }

        return $this->selectedMultiCurrency;
    }
}