<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\OrderList;

/**
 * Abstract order list
 */
abstract class AOrderList extends \XLite\View\Dialog
{
    /**
     * Orders list (cache)
     *
     * @var array(\XLite\Model\Order)
     */
    protected $orders = null;

    /**
     * Widget class name
     *
     * @var string
     */
    protected $widgetClass = '';

    /**
     * Get orders
     *
     * @return array(\XLite\Model\Order)
     */
    abstract public function getOrders();

    /**
     * Get widget keys
     *
     * @return array
     */
    abstract protected function getWidgetKeys();

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'order/list/list.css';

        return $list;
    }

    /**
     * Get class identifier as CSS class name
     *
     * @return string
     */
    public function getClassIdentifier()
    {
        return strtolower(str_replace('_', '-', $this->widgetClass));
    }

    /**
     * Get AJAX request parameters as javascript object definition
     *
     * @return string
     */
    public function getAJAXRequestParamsAsJSObject()
    {
        $params = $this->getAJAXRequestParams();

        $result = array();
        $forbidden = array('widgetTarget', 'widgetAction', 'widgetClass');

        foreach ($params as $key => $value) {
            if (!in_array($key, $forbidden)) {
                $result[] = $key . ': \'' . $value . '\'';
            }
        }

        return '{ '
            . 'widgetTarget: \'' . $params['widgetTarget'] . '\', '
            . 'widgetAction: \'' . $params['widgetAction'] . '\', '
            . 'widgetClass: \'' . $params['widgetClass'] . '\', '
            . 'widgetParams: { ' . implode(', ', $result) . ' }'
            . ' }';
    }

    /**
     * Check if the product of the order item is deleted one in the store
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param boolean                $data Flag
     *
     * @return boolean
     */
    public function checkIsAvailableToOrder(\XLite\Model\OrderItem $item, $data)
    {
        return $data !== $item->isValidToClone();
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'order/list';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getOrders();
    }

    /**
     * Get AJAX request parameters
     *
     * @return array
     */
    protected function getAJAXRequestParams()
    {
        $params = array(
            'widgetTarget' => \XLite\Core\Request::getInstance()->target,
            'widgetAction' => \XLite\Core\Request::getInstance()->action,
            'widgetClass'  => $this->widgetClass,
        );

        return $params + $this->getWidgetKeys();
    }

    /**
     * Check if the re-order button is shown
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function showReorder(\XLite\Model\Order $order)
    {
        $items = $order->getItems();
        return (bool)\Includes\Utils\ArrayManager::findValue(
            $items,
            array($this, 'checkIsAvailableToOrder'),
            false
        );
    }
}
