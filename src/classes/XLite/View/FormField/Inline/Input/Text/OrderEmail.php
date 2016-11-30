<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text;

/**
 * Email
 */
class OrderEmail extends \XLite\View\FormField\Inline\Input\Text\Email
{
    /**
     * Get additional CSS classes for the field widget
     *
     * @param array $field Field data
     *
     * @return string
     */
    protected function getAdditionalFieldStyle($field)
    {
        $style = parent::getAdditionalFieldStyle($field);

        return ($style ? $style . ' ' : '') . 'not-affect-recalculate';
    }

    /**
     * Save widget value in entity
     *
     * @param array $field Field data
     */
    protected function saveValueLogin($field)
    {
        $oldValue = $this->getEntity()->getLogin();
        $newValue = $field['widget']->getValue();

        if ($oldValue !== $newValue) {
            \XLite\Controller\Admin\Order::setOrderChanges(
                static::t('Email address'),
                $newValue,
                $oldValue
            );
        }

        $this->saveFieldValue($field);
    }
}
