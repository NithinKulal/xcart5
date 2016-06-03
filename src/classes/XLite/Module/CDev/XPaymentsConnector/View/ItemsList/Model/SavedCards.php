<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\ItemsList\Model;

/**
 * Saved credit cards items list
 */
class SavedCards extends \XLite\View\ItemsList\Model\Table
{

    // {{{ Definers

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'default' => array(
                static::COLUMN_NAME     => '',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_TEMPLATE => 'modules/CDev/XPaymentsConnector/account/default_radio.tpl',
                static::COLUMN_ORDERBY  => 100,
            ),
            'order' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Order'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'order',
                static::COLUMN_ORDERBY  => 200,
            ),
            'card' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Credit card'),
                static::COLUMN_TEMPLATE => 'modules/CDev/XPaymentsConnector/account/card.tpl',
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_ORDERBY  => 300,
            ),
            'address' => array(
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Billing address'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_MAIN     => true,
                static::COLUMN_TEMPLATE => 'modules/CDev/XPaymentsConnector/account/card_address.tpl',
                static::COLUMN_ORDERBY  => 400,
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
        return 'XLite\Module\CDev\XPaymentsConnector\Model\Payment\XpcTransactionData';
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
        return true;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
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
        return 'XLite\Module\CDev\XPaymentsConnector\View\StickyPanel\SavedCards';
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
     * Get customer profile 
     * 
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')
            ->find(\XLite\Core\Request::getInstance()->profile_id);
    }

    /**
     * Get order 
     * 
     * @param \XLite\Model\AEntity $entity Entity
     *  
     * @return \XLite\Model\Order
     */
    protected function getOrder(\XLite\Model\AEntity $entity)
    {
        return $entity
            ? $entity->getTransaction()->getOrder()
            : null;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $class = '\XLite\Module\CDev\XPaymentsConnector\Model\Repo\Payment\XpcTransactionData';

        $cnd->{$class::SEARCH_RECHARGES_ONLY} = true;
        $cnd->{$class::SEARCH_PAYMENT_ACTIVE} = true;
        $cnd->{$class::SEARCH_LOGIN} = $this->getCustomerProfile()->getLogin();

        return $cnd;
    }

    // }}}

    // {{{ Helpers

    /**
     * Check - specified card is default or not
     * 
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *  
     * @return boolean
     */
    public function isDefaultCard(\XLite\Model\AEntity $entity = null)
    {
        return $entity && $entity->getId() == $this->getCustomerProfile()->getDefaultCardId();
    }

    /**
     * Get list of addresses
     *
     * @return array
     */
    public function getAddressList()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressList($this->getCustomerProfile());
    }

    /**
     * Get list of addresses
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return bool
     */
    public function isSingleAddress()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->isSingleAddress($this->getCustomerProfile());;
    }

    /**
     * Get string linne for the single address
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return string
     */
    public function getSingleAddress(\XLite\Model\Profile $profile)
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getSingleAddress($this->getCustomerProfile());
    }

    /**
     * Get card address ID
     *
     * @param \XLite\Model\AEntity $entity Model OPTIONAL
     *
     * return int
     */
    public function getCardAddressId(\XLite\Model\AEntity $entity = null)
    {
        $resullt = 0;

        if (
            $entity
            && $entity->getBillingAddress()
        ) {
            $result = $entity->getBillingAddress()->getAddressId();
        }

        return $result;
    }

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
        $class = parent::getColumnClass($column, $entity);

        if ('card' == $column[static::COLUMN_CODE]) {
            $class .= ' ' . strtolower($entity->getCardType());
        }

        return $class;
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
        if ('order' == $column[static::COLUMN_CODE]) {
            $result = '#' . $this->getOrder($entity)->getOrderNumber();
        } else {
            $result = parent::getColumnValue($column, $entity);
        }

        return $result;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return 'order' == $column[static::COLUMN_CODE]
            ? \XLite\Core\Converter::buildURL(
                'order',
                '',
                array('order_number' => $this->getOrder($entity)->getOrderNumber())
            )
            : parent::buildEntityURL($entity, $column);
    }

    // }}}

}
