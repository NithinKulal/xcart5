<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
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
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\View;

use \XLite\Module\QSL\SpecialOffersBase\Logic\Order\Modifier\SpecialOffers;

/**
 * Widget that displays order item subtotals.
 */
class OrderItemSubtotal extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_ORDER_ITEM = 'item';
    const PARAM_ORDER_CART = 'cart';

    /**
     * Epsilon constant used when comparing float values.
     */
    const EPS = 0.000000001;

    /**
     * Item surcharges.
     *
     * @var array
     */
    protected $surcharges;

    /**
     * Add widget-specific styles.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = array(
            'file' => $this->getDir() . '/styles.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->isCustomSubtotalWidget() ?
            $this->getDir() . '/body.tpl'
            : 'shopping_cart/parts/item.subtotal.tpl';
    }

    /**
     * Returns the path to the folder with widget templates.
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/SpecialOffersBase/shopping_cart/item_subtotal';
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\Object(
                'Item',
                null,
                false,
                '\XLite\Model\OrderItem'
            ),
            static::PARAM_ORDER_CART => new \XLite\Model\WidgetParam\Object(
                'Cart',
                null,
                false,
                '\XLite\Model\Cart'
            ),
        );
    }

    /**
     * Returns the product option.
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }

    /**
     * Returns the product option.
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->getParam(self::PARAM_ORDER_CART);
    }

    /**
     * Check if the custom widget should be used for displaying order item subtotals.
     *
     * @return boolean
     */
    protected function isCustomSubtotalWidget()
    {
        return \XLite\Core\Config::getInstance()->QSL->SpecialOffersBase->soffersb_override_tpl;
    }

    /**
     * Check if the item has surcharges.
     *
     * @return boolean
     */
    protected function hasSurcharges()
    {
        return count($this->getSurcharges()) > 0;
    }

    /**
     * @return boolean
     */
    protected function isFreeItem()
    {
        return $this->getTotal() < static::EPS;
    }

    /**
     * Returns item surcharges that are not included into the item price.
     *
     * @return array
     */
    protected function getSurcharges()
    {
        if (!isset($this->surcharges)) {
            $this->defineSurcharges();
        }

        return $this->surcharges;
    }

    /**
     * Prepares the array with information on surcharges applied on the item.
     *
     * @return void
     */
    protected function defineSurcharges()
    {
        $this->surcharges = array();

        foreach ($this->getItem()->getExcludeSurcharges() as $surcharge) {
            $code = $surcharge->getCode();
            if (!isset($this->surcharges[$code])) {
                $this->surcharges[$code] = array(
                    'value'  => abs($surcharge->getValue()),
                    'label'  => $this->getSurchargeLabel($surcharge),
                    'models' => array($surcharge),
                );
            } else {
                $this->surcharges[$code]['value'] += abs($surcharge->getValue());
                $this->surcharges[$code]['models'][] = $surcharge;
            }
        }
    }

    /**
     * Returns the line item subtotal without any surcharges.
     *
     * @return float
     */
    protected function getSubtotal()
    {
        return $this->getItem()->getSubtotal();
    }

    /**
     * Returns the final line item subtotal including all applies item surcharges.
     *
     * @return float
     */
    protected function getTotal()
    {
        return $this->getItem()->getTotal();
    }

    /**
     * Returns the difference between the original subtotal and the subtotal including surcharges.
     *
     * @return float
     */
    protected function getDiscount()
    {
        return $this->getSubtotal() - $this->getTotal();
    }

    /**
     * Checks if the line item has a discount.
     *
     * @return boolean
     */
    protected function hasDiscount()
    {
        return $this->getDiscount() > static::EPS;
    }

    /**
     * Returns name/label for the surcharge.
     *
     * @param \XLite\Model\OrderItem\Surcharge $surcharge Surcharge.
     *
     * @return string
     */
    protected function getSurchargeLabel(\XLite\Model\OrderItem\Surcharge $surcharge)
    {
        if (SpecialOffers::MODIFIER_CODE == $surcharge->getCode()) {
            $label = $this->t('Special Offer discount');
        } else {
            $label = $this->t('Including X', array('name' => $surcharge->getName()));
        }

        return $label;
    }

    /**
     * Returns the order currency.
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return $this->getCart()->getCurrency();
    }
}