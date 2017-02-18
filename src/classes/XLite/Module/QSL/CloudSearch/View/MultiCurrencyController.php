<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Controller widget extension
 *
 * @Decorator\Depend({"XC\MultiCurrency", "QSL\CloudSearch"})
 */
class MultiCurrencyController extends \XLite\View\Controller implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getCloudSearchDynamicPricesEnabledCacheKey()
    {
        $key = parent::getCloudSearchDynamicPricesEnabledCacheKey();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        $key[] = $selectedCurrency->getCode();

        return $key;
    }

    /**
     * Enable dynamic prices if store is not on a default currency
     *
     * @return bool
     */
    protected function isCloudSearchDynamicPricesEnabled()
    {
        $mainCurrency = \XLite::getInstance()->getCurrency();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        return $mainCurrency->getCurrencyId() !== $selectedCurrency->getCurrency()->getCurrencyId()
               || parent::isCloudSearchDynamicPricesEnabled();
    }
}
