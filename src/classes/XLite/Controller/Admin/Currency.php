<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Currency management page controller
 */
class Currency extends \XLite\Controller\Admin\AAdmin
{
    /**
     * init
     *
     * @return void
     */
    public function init()
    {
        if (isset(\XLite\Core\Request::getInstance()->currency_id)) {
            $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->find(\XLite\Core\Request::getInstance()->currency_id);

            if ($currency) {
                $shopCurrency = \XLite\Core\Database::getRepo('XLite\Model\Config')
                    ->findOneBy(array('name' => 'shop_currency', 'category' => 'General'));

                \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                    $shopCurrency,
                    array('value' => $currency->getCurrencyId())
                );

                \XLite\Core\Config::updateInstance();
            }
        }
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Currency');
    }

    /**
     * Return currencies collection to use
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCurrencies()
    {
        if (!isset($this->currencies)) {
            $this->currencies = \XLite\Core\Database::getRepo('XLite\Model\Currency')->findAll();
        }

        return $this->currencies;
    }

    /**
     * Modify currency action
     *
     * @return void
     */
    protected function doActionModify()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * Class name for the \XLite\View\Model\ form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Currency\Currency';
    }
}
