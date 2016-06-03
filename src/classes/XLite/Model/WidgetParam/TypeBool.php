<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * ____description____
 */
class TypeBool extends \XLite\Model\WidgetParam\TypeSet
{
    /**
     * Values for TRUE value
     *
     * @var array
     */
    protected $trueValues = array('1', 'true', 1, true);

    /**
     * Options
     *
     * @var array
     */
    protected $options = array(
        'true'  => 'Yes',
        'false' => 'No',
    );

    /**
     * Get value by name
     *
     * @param mixed $name Value to get
     *
     * @return boolean
     */
    public function __get($name)
    {
        return $this->isTrue(parent::__get($name));
    }


    /**
     * Find if it is true value
     *
     * @param mixed $value Value of widget parameter
     *
     * @return boolean
     */
    protected function isTrue($value)
    {
        return in_array($value, $this->trueValues, true);
    }
}
