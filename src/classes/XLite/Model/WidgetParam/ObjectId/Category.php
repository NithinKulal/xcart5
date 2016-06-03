<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam\ObjectId;

/**
 * ____description____
 */
class Category extends \XLite\Model\WidgetParam\TypeObjectId
{
    /**
     * Allowed or not to  use root category id (0)
     *
     * @var boolean
     */
    protected $rootIsAllowed = false;


    /**
     * Constructor
     *
     * @param string  $label         Param label (text)
     * @param mixed   $value         Default value OPTIONAL
     * @param boolean $isSetting     Display this setting in CMS or not OPTIONAL
     * @param boolean $rootIsAllowed Root category id (0) is allowed or not OPTIONAL
     *
     * @return void
     */
    public function __construct($label, $value = null, $isSetting = false, $rootIsAllowed = false)
    {
        parent::__construct($label, $value, $isSetting);

        $this->rootIsAllowed = $rootIsAllowed;
    }


    /**
     * Return object class name
     *
     * @return string
     */
    protected function getClassName()
    {
        return '\XLite\Model\Category';
    }

    /**
     * getIdValidCondition
     *
     * @param mixed $value Value to check
     *
     * @return array
     */
    protected function getIdValidCondition($value)
    {
        $result = parent::getIdValidCondition($value);

        if ($this->rootIsAllowed) {
            $result = array(
                self::ATTR_CONDITION => 0 > $value,
                self::ATTR_MESSAGE   => ' is a negative number',
            );
        }

        return $result;
    }

    /**
     * getObjectExistsCondition
     *
     * @param mixed $value Value to check
     *
     * @return array
     */
    protected function getObjectExistsCondition($value)
    {
        $result = parent::getIdValidCondition($value);

        $result[self::ATTR_CONDITION] = 0 < $value && $result[self::ATTR_CONDITION];

        return $result;
    }
}
