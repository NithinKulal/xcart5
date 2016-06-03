<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Invoice widget
 *
 * @ListChild (list="order.children", weight="30")
 */
class PackingSlip extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ORDER = 'order';

    /**
     * Shipping modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $shippingModifier;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/packing_slip/style.css';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order',
                null,
                false,
                'XLite\Model\Order'
            ),
        );
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/packing_slip/body.twig';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder();
    }

    /**
     * Returns packing slip title
     *
     * @return string
     */
    protected function getPackingSlipTitle()
    {
        return static::t('Packing slip');
    }

    /**
     * Returns packing slip datetime
     *
     * @return string
     */
    protected function getPackingSlipDateTime()
    {
        return \XLite\Core\Converter::formatTime();
    }

    /**
     * Returns packing slip shippig method
     *
     * @return string
     */
    protected function getShippingMethodName()
    {
        return $this->getOrder()->getShippingMethodName();
    }

    /**
     * Get total qty
     *
     * @return integer
     */
    protected function getTotalQty()
    {
        return array_reduce(
            $this->getOrder()->getItems()->toArray(),
            function($carry, $item){
                return $carry + $item->getAmount();
            },
            0
        );
    }

    /**
     * Get total qty shipping
     *
     * @return integer
     */
    protected function getTotalQtyShip()
    {
        return $this->getTotalQty();
    }

    /**
     * Returns order items
     *
     * @return \XLite\Model\OrderItem[]
     */
    protected function getOrderItems()
    {
        return $this->getOrder()->getItems();
    }

    /**
     * Attributes visible
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isAttributesVisible(\XLite\Model\OrderItem $item)
    {
        return !$item->getAttributeValues()->isEmpty();
    }

    /**
     * Return specific data for address entry. Helper.
     *
     * @param \XLite\Model\Address $address   Address
     * @param boolean              $showEmpty Show empty fields OPTIONAL
     *
     * @return array
     */
    protected function getAddressSectionData(\XLite\Model\Address $address, $showEmpty = false)
    {
        $data = parent::getAddressSectionData($address, $showEmpty);
        $result = array();

        $name = array(
            'title'     => isset($data['title'])     ? $data['title']     : null,
            'firstname' => isset($data['firstname']) ? $data['firstname'] : null,
            'lastname'  => isset($data['lastname'])  ? $data['lastname']  : null,
        );

        foreach ($data as $serviceName => $field) {
            switch ($serviceName) {
                case 'title':
                case 'firstname':
                case 'lastname':
                    $result += array_filter($name);
                    unset($data['title'], $data['firstname'], $data['lastname']);
                    break;
                default:
                    $result[$serviceName] = $field;
                    break;
            }
        }

        return $result;
    }
}
