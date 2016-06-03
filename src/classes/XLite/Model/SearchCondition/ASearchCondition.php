<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition;

/**
 * ASearchCondition
 */
abstract class ASearchCondition extends \XLite\Base\SuperClass
{
    /**
     * Expression value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Expression value setter
     *
     * @return \XLite\Model\SearchCondition\IExpressionProvider
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Expression value getter
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
