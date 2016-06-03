<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition\Expression;

/**
 * TypeEquality
 */
class TypeEquality extends Base
{
    protected function getDefaultParameterNameSuffix()
    {
        return '_equality_value';
    }


    protected function getOperator()
    {
        return '=';
    }
}
