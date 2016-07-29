<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model;

/**
 * Saved credit cards items list
 */
class PaymentMethods extends \XLite\View\ItemsList\Model\Table
{

    // {{{ Definers

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $listTemplateDir = 'modules/CDev/XPaymentsConnector/settings/payment_methods/'; 

        return array(
            'name' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Payment method'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_TEMPLATE => $listTemplateDir . 'list.name.twig',
                static::COLUMN_ORDERBY  => 100,
            ),
            'currency' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Currency'),
                static::COLUMN_NO_WRAP  => false,
                static::COLUMN_TEMPLATE => $listTemplateDir . 'list.currency.twig',
                static::COLUMN_ORDERBY  => 200,
            ),
            'save_cards' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Save cards'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => $listTemplateDir . 'list.save_cards.twig',
                static::COLUMN_ORDERBY  => 800,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Payment\Method';
    }

    // }}}

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\CDev\XPaymentsConnector\View\StickyPanel\PaymentMethods';
    }

    // }}}

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_CLASS}
            = 'Module\CDev\XPaymentsConnector\Model\Payment\Processor\XPayments';

        $cnd->{\XLite\Model\Repo\Payment\Method::P_ORDER_BY} = array('translations.name', 'asc');

        return $cnd;
    }

    // }}}

    /**
     * Get column cell class
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * @return string
     */
    protected function getColumnClass(array $column, \XLite\Model\AEntity $entity = null)
    {
        return parent::getColumnClass($column, $entity);
    }

    /**
     * Get column value
     *
     * @param array                $column Column
     * @param \XLite\Model\AEntity $entity Model
     *
     * @return mixed
     */
    protected function getColumnValue(array $column, \XLite\Model\AEntity $entity)
    {
        if ('name' == $column[static::COLUMN_CODE]) {
            $result = parent::getColumnValue($column, $entity);
        }

        return $result;
    }

    /**
     * Check - 'save cards' options is enabled or not
     * 
     * @param \XLite\Model\AEntity $entity Entity
     *  
     * @return boolean
     */
    protected function isSaveCards(\XLite\Model\AEntity $entity)
    {
        return $entity->getSetting('saveCards') == 'Y';
    }

    /**
     * Check if MultiCurrency is used
     *
     * @return bool
     */
    protected static function isMultiCurrency()
    {
        return class_exists('\XLite\Module\XC\MultiCurrency\Core\MultiCurrency');
    }

    /**
     * Check if currency is supported by the store
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isCurrencySupported(\XLite\Model\AEntity $entity)
    {
        // Store currency
        $xcCurrencyCode = \XLite::getInstance()->getCurrency()->getCode();

        // X-Payments payment configuration currency
        $xpcCurrencyCode = $this->getPaymentMethodCurrency($entity)->getCode();

        return $xcCurrencyCode == $xpcCurrencyCode;
    }

    /**
     * Get currency for payment method
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return \XLite\Model\Currency 
     */
    protected function getPaymentMethodCurrency(\XLite\Model\AEntity $entity)
    {
        $currencyCode = $entity->getSetting('currency')
            ? $entity->getSetting('currency')
            : \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_currency;

        $currency = is_numeric($currencyCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\Currency')->find($currencyCode)
            : \XLite\Core\Database::getRepo('XLite\Model\Currency')->findByCode($currencyCode);

        if (!$currency) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        return $currency;
    }

    /**
     * Currency title 
     *
     * @param \XLite\Model\Currency $currency Currency
     *
     * @return string
     */
    protected function getCurrencyTitle(\XLite\Model\Currency $currency)
    {
        return sprintf('%s - %s', $currency->getCode(), $currency->getName());
    }

    /**
     * Get link to the currency settings
     *
     * @return string
     */
    protected function getCurrencySettingsLink()
    {
        $link = static::isMultiCurrency()
            ? \XLite\Core\Converter::buildURL('currencies')
            : \XLite\Core\Converter::buildURL('currency');

        return $link;
    }

    /**
     * Tooltip content for not supported currency
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getCurrencyTooltipContent(\XLite\Model\AEntity $entity)
    {
        $text = static::t(
            'Store is configured to process payments in <strong>{{currency}}</strong>. <a href="{{link}}" target="_blank">Check settings</a>.',
            array(
                'currency' => $this->getCurrencyTitle(\XLite::getInstance()->getCurrency()),
                'link' => $this->getCurrencySettingsLink(),
            )
        );

        // Link to the payment configuration settings in X-Payments
        $xpLink = \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_xpayments_url
            . 'admin.php?target=payment_conf&conf_id=' . $entity->getSetting('id'); 

        $text .= '<br/>' . static::t(
            'Or ajust payment configuration settings in <a href="{{link}}" target="_blank">X-Payments</a>.',
            array(
                'link' => $xpLink,
            )
        );

        return $text;
    }
}
