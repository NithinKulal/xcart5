<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * Collection (array)
 */
class TypeCollection extends \XLite\Model\WidgetParam\AWidgetParam
{
    /**
     * Param type
     *
     * @var string
     */
    protected $type = 'list';


    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     */
    protected function getValidaionSchema($value)
    {
        return array(
            array(
                self::ATTR_CONDITION => !is_array($value),
                self::ATTR_MESSAGE   => ' is not an array',
            ),
        );
    }
}
