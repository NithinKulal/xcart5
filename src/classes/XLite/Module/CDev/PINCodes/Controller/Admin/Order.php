<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Admin;

/**
 * Order modify
 *
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $tpls = parent::getPageTemplates();

        if ($this->getOrder() && $this->getOrder()->hasPinCodes()) {
            $tpls += array(
                'pin_codes' => 'modules/CDev/PINCodes/order/pin_codes.twig',
            );
        }

        return $tpls;
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();
        if ($this->getOrder() && $this->getOrder()->hasPinCodes()) {
            $pages['pin_codes'] = static::t('PIN codes');
        }

        return $pages;
    }

    /**
     * Get order items
     *
     * @return array
     */
    public function getOrderItems()
    {
        return $this->getOrder()->getItems();
    }

    /**
     * Remove temporary order
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return void
     */
    protected function removeTemporaryOrder(\XLite\Model\Order $order)
    {
        foreach ($order->getItems() as $item) {
            foreach ($item->getPinCodes() as $pin) {
                \XLite\Core\Database::getEM()->remove($pin);
            }
        }

        parent::removeTemporaryOrder($order);
    }
}
