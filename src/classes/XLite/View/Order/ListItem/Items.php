<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\ListItem;

/**
 * Order items list (short version)
 */
class Items extends \XLite\View\AView
{
    /**
     *  Widget parameters names
     */
    const PARAM_ORDER    = 'order';
    const PARAM_ORDER_ID = 'order_id';
    const PARAM_FULL     = 'full';


    /**
     * Order items list maximum length
     *
     * @var integer
     */
    protected $orderItemsMax = 3;

    /**
     * Order (cache)
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if (null === $this->order) {
            $this->order = false;

            if ($this->getParam(self::PARAM_ORDER) instanceof \XLite\Model\Order) {
                // order based
                $this->order = $this->getParam(self::PARAM_ORDER);

            } elseif (0 < $this->getRequestParamValue(self::PARAM_ORDER_ID)) {
                // order id based
                $order = new \XLite\Model\Order($this->getRequestParamValue(self::PARAM_ORDER_ID));

                if ($order->isPersistent()) {
                    $this->order = $order;
                }
            }
        }

        return $this->order;
    }

    /**
     * Get order id
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->getOrder()->get('order_id');
    }

    /**
     * Get order items
     *
     * @return array(\XLite\Model\OrderItem)
     */
    public function getItems()
    {
        return $this->getRequestParamValue(self::PARAM_FULL)
            ? $this->getOrder()->getItems()
            : array_slice($this->getOrder()->getItems()->toArray(), 0, $this->orderItemsMax);
    }

    /**
     * Check - link to full items list is visible or not
     *
     * @return boolean
     */
    public function isMoreLinkVisible()
    {
        return $this->orderItemsMax < count($this->getOrder()->getItems());
    }

    /**
     * Get list to full items list class name
     *
     * @return string
     */
    public function getMoreLinkClassName()
    {
        return $this->getRequestParamValue(self::PARAM_FULL) ? 'open' : 'close';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/order/items.twig';
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
            self::PARAM_ORDER    => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, '\XLite\Model\Order'),
            self::PARAM_ORDER_ID => new \XLite\Model\WidgetParam\TypeInt('Order id', null, false),
            self::PARAM_FULL     => new \XLite\Model\WidgetParam\TypeBool('Display full list', false),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getOrder();
    }
}
