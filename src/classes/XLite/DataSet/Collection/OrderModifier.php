<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\DataSet\Collection;

/**
 * Order modifiers collection
 */
class OrderModifier extends \XLite\DataSet\Collection
{
    /**
     * Check element
     *
     * @param mixed $element Element
     * @param mixed $key     Element key
     *
     * @return boolean
     */
    protected function checkElement($element, $key)
    {
        return parent::checkElement($element, $key)
            && $element instanceof \XLite\Model\Order\Modifier;
    }

}
