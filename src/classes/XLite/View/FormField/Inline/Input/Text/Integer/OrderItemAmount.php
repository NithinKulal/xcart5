<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Integer;

/**
 * Order item amount
 */
class OrderItemAmount extends \XLite\View\FormField\Inline\Input\Text\Integer
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Text\OrderItemAmount';
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $params = parent::getFieldParams($field) + array(
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN              => 1,
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_CTRL => false,
        );

        $max = $this->getMaxValue();
        if (isset($max)) {
            $params[\XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MAX] = $max;
        }

        return $params;
    }

    /**
     * Get quantity maximum
     *
     * @return integer
     */
    protected function getMaxValue()
    {
        return $this->getEntity()->getProductAvailableAmount() + $this->getEntity()->getAmount();
    }
}
