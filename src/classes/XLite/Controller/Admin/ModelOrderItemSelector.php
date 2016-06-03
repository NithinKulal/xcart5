<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * OrderItem model selector controller
 */
class ModelOrderItemSelector extends \XLite\Controller\Admin\ModelProductSelector
{
    /**
     * Cached order item
     *
     * @var \XLite\Model\OrderItem
     */
    protected $orderItem;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Define specific data structure which will be sent in the triggering event (model.selected)
     *
     * @param mixed $item Model item
     *
     * @return string
     */
    protected function defineDataItem($item)
    {
        $data = parent::defineDataItem($item);

        $orderItem = new \XLite\Model\OrderItem();
        $orderItem->setProduct($item);

        if ($item->hasEditableAttributes()) {
            $orderItem->setAttributeValues($item->prepareAttributeValues());
        }

        $orderItem = $this->postprocessOrderItem($orderItem);

        $orderItem->setItemNetPrice(null);
        $orderItem->setPrice(null);
        $orderItem->calculate();
        $orderItem->renew();

        $data['clear_price'] = $orderItem->getItemPrice();
        $data['selected_price'] = $orderItem->getDisplayPrice();

        $data['max_qty'] = $orderItem->getProductAvailableAmount();
        $data['server_price_control'] = $orderItem->isPriceControlledServer();

        if ($item->hasEditableAttributes()) {
            // SKU may differ after attributes selection
            $data['selected_sku'] = $orderItem->getSku();
            $data['presentation'] = $this->formatItem($orderItem);

            $data['clear_price'] = $orderItem->getItemPrice();
            $data['server_price_control'] = $orderItem->isPriceControlledServer();

            if ($data['server_price_control']) {
                $data['selected_price'] = $orderItem->getDisplayPrice();
            }

            $widget = new \XLite\View\OrderItemAttributes(
                array(
                    'orderItem' => $orderItem,
                    'idx'       => \XLite\Core\Request::getInstance()->idx ?: $orderItem->getItemId(),
                )
            );
            $widget->init();

            $data['selected_attributes'] = $widget->getContent();

            $widget = new \XLite\View\InvoiceAttributeValues(
                array(
                    'item'             => $orderItem,
                    'displayVariative' => 1,
                )
            );

            $widget->init();

            $data['attributes_widget'] = $widget->getContent();
        }

        $this->orderItem = $orderItem;

        return $data;
    }

    /**
     * Do additional modifications with order item and return this
     *
     * @param \XLite\Model\OrderItem $orderItem Order item entity
     *
     * @return \XLite\Model\OrderItem
     */
    protected function postprocessOrderItem(\XLite\Model\OrderItem $orderItem)
    {
        return $orderItem;
    }

    /**
     * Format the value for the method: $this->getJSONData()
     *
     * @param mixed   &$item
     * @param integer $index
     *
     * @return void
     */
    public function prepareItem(&$item, $index)
    {
        parent::prepareItem($item, $index);

        $item['text_presentation'] = $item['data']['presentation'];
    }
}
