<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

// TODO - must be a parent of the Repo classes
// TODO - must be completely revised after the multiple inheritance will be added

/**
 * Searchable
 */
abstract class Searchable extends \XLite\Base\SuperClass
{
    /**
     * Prepare the "LIMIT" SQL condition
     *
     * @param integer                $start First item index
     * @param integer                $count Items per frame
     * @param \XLite\Core\CommonCell $cnd   Condition object to use OPTIONAL
     *
     * @return \XLite\Core\CommonCell
     */
    public static function addLimitCondition($start, $count, \XLite\Core\CommonCell $cnd = null)
    {
        if (!isset($cnd)) {
            $cnd = new \XLite\Core\CommonCell();
        }
        // TODO - must be "self::P_LIMIT"
        $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = array($start, $count);

        return $cnd;
    }
}
