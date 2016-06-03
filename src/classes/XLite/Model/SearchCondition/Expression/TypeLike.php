<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\SearchCondition\Expression;

/**
 * TypeLike
 */
class TypeLike extends Base
{
    protected $format = 'WHOLE';

    public function setFormat($format)
    {
        $this->format = $format;
    }

    protected function preprocessValue($value)
    {
        $preprocessed = $value;

        switch ($this->format) {
            case 'LEFT':
                $preprocessed = '%' . $value;
                break;

            case 'RIGHT':
                $preprocessed = $value . '%';
                break;

            case 'WHOLE':
            default:
                $preprocessed = '%' . $value . '%';
                break;
        }

        return $preprocessed;
    }

    protected function getDefaultParameterNameSuffix()
    {
        return '_like_value';
    }

    protected function getOperator()
    {
        return 'LIKE';
    }
}
