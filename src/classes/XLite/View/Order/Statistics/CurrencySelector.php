<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Statistics;

/**
 * Currency selector
 */
class CurrencySelector extends \XLite\View\AView
{
    /**
     * Current currency
     *
     * @var \XLite\Model\Currency
     */
    protected $currency;

    /**
     * Returns JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'order/currency_selector.js';

        return $list;
    }

    /**
     * Returns JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/currency_selector.css';

        return $list;
    }

    /**
     * Returns current currency code
     *
     * @return string
     */
    protected function getCurrentCurrencyId()
    {
        if (!isset($this->currency)) {
            if (\XLite\Core\Request::getInstance()->currency) {
                $this->currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                    ->find(\XLite\Core\Request::getInstance()->currency);
            }

            if (!$this->currency) {
                $this->currency = \XLite::getInstance()->getCurrency();
            }
        }

        return $this->currency->getCurrencyId();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/currency_selector.twig';
    }
}
