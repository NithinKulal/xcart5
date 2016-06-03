<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\View\ItemsList\BulkEditing;

abstract class AProduct extends \XLite\Module\XC\BulkEditing\View\ItemsList\BulkEditing\ABulkEditing
{
    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\Module\XC\BulkEditing\View\Pager\BulkEditing';
    }

    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    protected function getItemName($item)
    {
        return $item->getName();
    }
}
