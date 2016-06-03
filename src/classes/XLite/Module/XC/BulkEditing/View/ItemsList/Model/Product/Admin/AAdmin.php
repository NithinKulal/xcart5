<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\Model\Product\Admin;

abstract class AAdmin extends \XLite\View\ItemsList\Model\Product\Admin\AAdmin implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(parent::getFormParams(), ['scenario' => '']);
    }
}
