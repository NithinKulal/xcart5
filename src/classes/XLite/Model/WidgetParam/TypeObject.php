<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * Object type widget param
 */
class TypeObject extends \XLite\Model\WidgetParam\AWidgetParam
{
    /**
     * class
     *
     * @var mixed
     */
    protected $class;

    /**
     * Constructor
     *
     * @param mixed  $label     Param label (text)
     * @param mixed  $value     Default value OPTIONAL
     * @param mixed  $isSetting Display this setting in CMS or not OPTIONAL
     * @param string $class     Object class OPTIONAL
     */
    public function __construct($label, $value = null, $isSetting = false, $class = null)
    {
        parent::__construct($label, $value, $isSetting);

        // TODO - check if there are more convinient way to extend this class
        if (null === $this->class) {
            $this->class = $class;
        }
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return array
     */
    protected function getValidaionSchema($value)
    {
        return array(
            array(
                self::ATTR_CONDITION => is_object($value),
                self::ATTR_MESSAGE   => ' passed value is not an object',
            ),
            array(
                self::ATTR_CONDITION => null === $this->class || $value instanceof $this->class,
                self::ATTR_MESSAGE   => ' parameter class is undefined or passed object is not an instance of the param class',
            ),
        );
    }
}
