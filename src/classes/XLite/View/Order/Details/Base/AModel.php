<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Base;

/**
 * AModel
 */
abstract class AModel extends \XLite\View\Model\AModel
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'order';

        return $result;
    }

    /**
     * Return current order ID
     *
     * NOTE: this method is public since it's used
     * by the external widgets (e.g. forms)
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->getOrder()->getOrderId();
    }

    /**
     * This object will be used if another one is not pased
     *
     * @return \XLite\Model\Order
     */
    protected function getDefaultModelObject()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Order\Details\Admin\Form';
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Order #{{id}} details', array('id' => $this->getOrderId()));
    }
}
