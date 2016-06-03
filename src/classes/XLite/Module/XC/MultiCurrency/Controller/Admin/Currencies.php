<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Admin;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Currencies management page controller
 */
class Currencies extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Localization');
    }

    /**
     * Do action 'add_currency'
     *
     * @return void
     */
    public function doActionAddCurrency()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        if (
            isset($data['currency_id'])
            && !empty($data['currency_id'])
        ) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->addCurrency($data['currency_id']);
        }
    }

    /**
     * Do action 'update'
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        $configTable = \XLite\Core\Database::getInstance()->getTablePrefix() . 'config';

        if (
            isset($data['delete'])
            && !empty($data['delete'])
        ) {
            foreach ($data['delete'] as $id => $value) {
                $activeCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                    ->find($id);

                if (isset($activeCurrency)) {
                    if (!$activeCurrency->isDefaultCurrency()) {
                        $activeCurrency->delete();
                    } else {
                        \XLite\Core\TopMessage::addError('err_change_default_currency');
                    }
                }

                if (isset($data['data'][$id])) {
                    unset($data['data'][$id]);
                }
            }
        }

        foreach ($data['data'] as $id => $row) {
            $activeCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->find($id);

            if (isset($activeCurrency)) {
                $activeCurrency->setPosition($row['position']);

                if (!$activeCurrency->isDefaultCurrency()) {
                    if ($row['rate'] != $activeCurrency->getRate()) {
                        $activeCurrency->setRate($row['rate']);
                    }

                    $activeCurrency->setEnabled($row['enabled']);
                } elseif (
                    $activeCurrency->isDefaultCurrency()
                    && (
                        $row['rate'] != $activeCurrency->getRate()
                        || $row['enabled'] != $activeCurrency->getEnabled()
                    )
                ) {
                    \XLite\Core\TopMessage::addError('err_change_default_currency');
                }

                $activeCurrency->setPrefix($row['prefix']);
                $activeCurrency->setSuffix($row['suffix']);
                $activeCurrency->setFormat($row['format']);

                $activeCurrency->update();
            }
        }

        if (
            isset($data['defaultValue'])
            && !empty($data['defaultValue'])
        ) {
            $newDefaultCurrency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->find($data['defaultValue']);

            if (
                isset($newDefaultCurrency)
                && !$newDefaultCurrency->isDefaultCurrency()
            ) {
                \XLite\Core\Database::getEM()->getConnection()->exec(
                    "UPDATE $configTable SET value='"
                    . $newDefaultCurrency->getCurrency()->getCurrencyId()
                    . '\' WHERE category=\'General\' AND name=\'shop_currency\''
                );

                \XLite\Core\Config::updateInstance();

                $newDefaultCurrency->setEnabled(1);
                $newDefaultCurrency->setRate(1);
                $newDefaultCurrency->setRateDate(0);

                $newDefaultCurrency->update();
            }
        }

        if (isset($data['rate_provider'])) {
            \XLite\Core\Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . $data['rate_provider']
                . '\' WHERE category=\'XC\\\\MultiCurrency\' AND name=\'rateProvider\''
            );
        }

        if (isset($data['update_interval'])) {
            \XLite\Core\Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . $data['update_interval']
                . '\' WHERE category=\'XC\\\\MultiCurrency\' AND name=\'updateInterval\''
            );
        }

        if (isset($data['trailing_zeroes'])) {
            \XLite\Core\Database::getEM()->getConnection()->exec(
                "UPDATE $configTable SET value='"
                . ($data['trailing_zeroes'] == 1 ? 1 : 0)
                . '\' WHERE category=\'General\' AND name=\'trailing_zeroes\''
            );
        }

        \XLite\Core\Config::updateInstance();

        MultiCurrency::getInstance()->updateRates();
    }

    /**
     * Do action 'update_rates'
     *
     * @return void
     */
    public function doActionUpdateRates()
    {
        MultiCurrency::getInstance()->updateRates();
    }

    /**
     * Check if the form ID validation is needed
     *
     * @return boolean
     */
    protected function isActionNeedFormId()
    {
        return parent::isActionNeedFormId() && ('update_rates' != $this->getAction());
    }
}