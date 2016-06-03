<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Widget generates content for order history event ORDER_EDITED
 */
class OrderEditHistoryData extends \XLite\View\AView
{
    /**
     *  Widget parameters names
     */
    const PARAM_CHANGES = 'changes';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'order/history/order_changes.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CHANGES => new \XLite\Model\WidgetParam\TypeCollection('Array of order changes', array()),
        );
    }

    /**
     * Get array if changes
     *
     * @return array
     */
    protected function getChanges()
    {
        return $this->getParam(static::PARAM_CHANGES);
    }

    /**
     * Return true is value is array
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isArray($value)
    {
        return is_array($value) && !(2 == count($value) && isset($value['old']) && isset($value['new']));
    }

    /**
     * Return true if subname should be displayed
     *
     * @param mixed $subname Subname
     *
     * @return boolean
     */
    protected function isDisplaySubname($subname)
    {
        return !is_numeric($subname);
    }
}
