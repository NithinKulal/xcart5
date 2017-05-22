<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Admin;

/**
 * Admin order messages
 */
class Order extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\Order
{

    /**
     * @inheritdoc
     */
    protected static function getWidgetTarget()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return array(
            'order_number' => $this->getOrder()->getOrderNumber(),
            'page'         => 'messages',
        );
    }

    /**
     * @inheritdoc
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += array(
                'order_number' => $this->getOrder()->getOrderNumber(),
                'page'         => 'messages',
            );
        }

        return $this->commonParams;
    }

    /**
     * Get open URL
     *
     * @return string
     */
    protected function getOpenURL()
    {
        return static::buildURL(
            'order',
            null,
            array(
                'order_number' => $this->getOrder()->getOrderNumber(),
                'page'         => 'messages',
                'display_all'  => 1,
            )
        );
    }

    /**
     * Get close URL
     *
     * @return string
     */
    protected function getCloseURL()
    {
        return static::buildURL(
            'order',
            null,
            array(
                'order_number' => $this->getOrder()->getOrderNumber(),
                'page'         => 'messages',
                'display_all'  => 0,
            )
        );
    }

    // }}}
}
