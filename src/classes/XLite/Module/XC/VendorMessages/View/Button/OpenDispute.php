<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Button;

/**
 * Link to messages list
 */
class OpenDispute extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_ORDER        = 'order';
    const PARAM_RECIPIENT_ID = 'recipient_id';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $order = method_exists(\XLite::getController(), 'getOrder')
            ? \XLite::getController()->getOrder()
            : null;

        $this->widgetParams += array(
            static::PARAM_ORDER        => new \XLite\Model\WidgetParam\TypeObject('Order ID', $order, false, 'XLite\Model\Order'),
            static::PARAM_RECIPIENT_ID => new \XLite\Model\WidgetParam\TypeInt('Recipient (suborder) ID', 0),
        );
    }

    /**
     * Return target order ID
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(static::PARAM_ORDER);
    }

    /**
     * Return target recipient ID
     *
     * @return integer
     */
    protected function getRecipientId()
    {
        return $this->getParam(static::PARAM_RECIPIENT_ID);
    }

    /**
     * @inheritdoc
     */
    protected function prepareURLParams()
    {
        return \XLite::isAdminZone()
            ? array(
                'target'             => 'order',
                'widget'             => '\XLite\Module\XC\VendorMessages\View\Popup\Dispute',
                'order_number'       => $this->getOrder()->getOrderNumber(),
                'page'               => 'messages',
                'recipient_id'       => $this->getRecipientId(),
                'open_dispute_popup' => 1,
            )
            : array(
                'target'             => 'order_messages',
                'widget'             => '\XLite\Module\XC\VendorMessages\View\Popup\Dispute',
                'order_number'       => $this->getOrder()->getOrderNumber(),
                'recipient_id'       => $this->getRecipientId(),
                'open_dispute_popup' => 1,
            );
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultLabel()
    {
        return 'Open dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getClass()
    {
        return parent::getClass() . ' open-dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return \XLite::isAdminZone()
            ? parent::getDefaultTemplate()
            : 'modules/XC/VendorMessages/button/popup_button.twig';
    }

}
