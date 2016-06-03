<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\FormModel;

class FormGenerator extends \XLite\View\FormModel\FormGenerator implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getTypeExtensions()
    {
        $list = parent::getTypeExtensions();
        $list[] = 'XLite\Module\XC\BulkEditing\View\FormModel\Type\Base\BulkEditTypeExtension';

        return $list;
    }
}
