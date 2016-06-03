<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Switcher
 */
class NotificationStatus extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $list = parent::getFieldParams($field);

        $entity = $this->getEntity();

        if (
            (!$entity->getAvailableForAdmin() && $entity->getEnabledForAdmin())
            || (!$entity->getAvailableForCustomer() && $entity->getEnabledForCustomer())
        ) {
            $list[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
        }

        return $list;
    }
}

