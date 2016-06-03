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
 * @ListChild (list="center")
 */
class InvoicePage extends \XLite\View\Dialog
{
    /**
     * Order (cache)
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'invoice';

        return $result;
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

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Order #{{id}}', array('id' => $this->getOrder()->getOrderId()));
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'order/invoice';
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return $this->getDir() . '/page.twig';
    }
}
