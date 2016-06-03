<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * Abstract Object id widget parameter
 */
abstract class TypeObjectId extends \XLite\Model\WidgetParam\TypeInt
{
    /**
     * Return object class name
     *
     * @return string
     */
    abstract protected function getClassName();


    /**
     * Return object with passed/predefined ID
     *
     * @param integer $id Object ID OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function getObject($id = null)
    {
        return \XLite\Core\Database::getRepo($this->getClassName())->find($this->getId($id));
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
        return array(
            self::ATTR_CONDITION => 0 >= $value,
            self::ATTR_MESSAGE   => ' is a non-positive number',
        );
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
        return array(
            self::ATTR_CONDITION => !is_object($this->getObject($value)),
            self::ATTR_MESSAGE   => ' record with such ID is not found',
        );
    }

    /**
     * Return object ID
     *
     * @param integer $id Object ID OPTIONAL
     *
     * @return integer
     */
    protected function getId($id = null)
    {
        return isset($id) ? $id : $this->value;
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return void
     */
    protected function getValidaionSchema($value)
    {
        $schema = parent::getValidaionSchema($value);
        $schema[] = $this->getIdValidCondition($value);
        $schema[] = $this->getObjectExistsCondition($value);

        return $schema;
    }
}
