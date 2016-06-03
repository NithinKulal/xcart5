<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Submit button for export products
 *
 */
class ExportProducts extends \XLite\View\Button\Submit
{
    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Export products';
    }

    /**
     * getClass
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' export-products';
    }
}
