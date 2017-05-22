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
class Messages extends \XLite\View\Button\Link
{
    /**
     * Widget parameter names
     */
    const PARAM_ORDER = 'order';

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/VendorMessages/button.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, 'XLite\Model\Order'),
        );
    }

    /**
     * @inheritdoc
     */
    protected function getLocationURL()
    {
        $order = $this->getParam(static::PARAM_ORDER) ?: $this->getOrder();

        return \XLite::getInstance()->getShopURL(
            static::buildURL(
                'order_messages',
                null,
                array(
                    'order_number' => $order->getOrderNumber(),
                )
            )
        );
    }

    /**
     * @inheritdoc
     */
    protected function getButtonLabel()
    {
        $order = $this->getParam(static::PARAM_ORDER) ?: $this->getOrder();

        $label = static::t('Messages');
        $count = $order->countUnreadMessages();
        if ($count > 0) {
            $label .= ' (' . static::t('X unread messages', array('count' => $count)) . ')';
        }

        return $label;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultStyle()
    {
        return trim(parent::getDefaultStyle() . ' messages');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultButtonType()
    {
        return 'btn-link';
    }


}
