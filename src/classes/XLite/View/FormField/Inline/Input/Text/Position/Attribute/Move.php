<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Position\Attribute;

/**
 * Order by position (soratable)
 */
class Move extends \XLite\View\FormField\Inline\Input\Text\Position\Move
{
    /**
     * Preprocess value before save: return 1 or 0
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function preprocessValueBeforeSave($value)
    {
        return array(
            'position' => $value,
            'product'  => $this->getProduct(),
        );
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return $this->getEntity()->getPosition($this->getProduct());
    }
}
