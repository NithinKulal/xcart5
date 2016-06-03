<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product\Modify;

/**
 * Attributes
 */
class Attributes extends \XLite\View\Form\Product\Modify\Base\Single
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_attributes';
    }
}
