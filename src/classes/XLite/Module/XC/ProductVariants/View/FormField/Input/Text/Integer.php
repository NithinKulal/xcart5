<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input\Text;

/**
 * Integer
 */
class Integer extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
       return '' !== $this->getValue() ? parent::sanitize() : '';
    }
}
