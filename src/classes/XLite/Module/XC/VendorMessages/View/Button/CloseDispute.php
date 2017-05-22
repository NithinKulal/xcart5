<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\Button;

/**
 * Close dispute
 */
class CloseDispute extends \XLite\View\Button\ConfirmRegular
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
    protected function getDefaultAction()
    {
        return 'close_dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getJSFormParams(array $params)
    {
        $params['order_number'] = $this->getOrder()->getOrderNumber();
        $params['recipient_id'] = $this->getRecipientId();

        if (\XLite::isAdminZone()) {
            $params['target'] = 'order';
            $params['page'] = 'messages';

        } else {
            $params['target'] = 'order_messages';
        }

        return parent::getJSFormParams($params);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultLabel()
    {
        return 'Close dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getClass()
    {
        return parent::getClass() . ' close-dispute';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return \XLite::isAdminZone()
            ? parent::getDefaultTemplate()
            : 'modules/XC/VendorMessages/button/regular.twig';
    }

}
