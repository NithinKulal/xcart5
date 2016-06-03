<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View\Order;

/**
 * Order item box
 *
 * @ListChild (list="invoice.item.name", zone="customer")
 * @ListChild (list="invoice.item.name", zone="mail")
 */
class ItemBox extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_ITEM = 'item';

   /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, 'XLite\Model\OrderItem'),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getAttachments();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/item.twig';
    }

    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        return $this->getItem()
            ? $this->getItem()->getPrivateAttachments()->toArray()
            : array();
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(static::PARAM_ITEM);
    }
}
