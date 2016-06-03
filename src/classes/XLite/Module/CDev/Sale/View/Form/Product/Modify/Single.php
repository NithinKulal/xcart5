<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Form\Product\Modify;

/**
 * Form class
 */
class Single extends \XLite\View\Form\Product\Modify\Single implements \XLite\Base\IDecorator
{
    /**
     * Set validators pairs for products data
     *
     * @param mixed &$data Data
     *
     * @return void
     */
    protected function setDataValidators(&$data)
    {
        parent::setDataValidators($data);

        $this->setSaleDataValidators($data);
    }
}
