<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Service name of the address field
 */
class UniqueField extends \XLite\Core\Validator\AValidator
{
    /**
     * Field class for checking
     *
     * @var string
     */
    protected $fieldClass;

    /**
     * Field name for checking
     *
     * @var string
     */
    protected $fieldName;

    /**
     * Field value for checking (saved)
     *
     * @var string
     */
    protected $fieldValue;

    /**
     * Constructor
     *
     * @param mixed $fieldClass Field class OPTIONAL
     * @param mixed $fieldName  Field identifier OPTIONAL
     * @param mixed $fieldValue Field value OPTIONAL
     *
     * @return void
     */
    public function __construct($fieldClass = null, $fieldName = null, $fieldValue = null)
    {
        parent::__construct();

        if (isset($fieldClass)) {
            $this->fieldClass = $fieldClass;
        }

        if (isset($fieldName)) {
            $this->fieldName = $fieldName;
        }

        if (isset($fieldValue)) {
            $this->fieldValue = $fieldValue;
        }
    }

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function validate($data)
    {
        if (!\XLite\Core\Converter::isEmptyString($data)) {
            $entity = \XLite\Core\Database::getRepo($this->fieldClass)->findOneBy(
                array(
                    $this->fieldName => $this->sanitize($data),
                )
            );

            // DO NOT use "!==" here
            if (
                $entity
                && (
                    empty($this->fieldValue)
                    || $entity->
                        {'get' . \XLite\Core\Converter::convertToCamelCase($this->fieldName)}() != $this->fieldValue
                )
            ) {
                $this->throwSKUError();
            }
        }
    }

    /**
     * Sanitize
     *
     * @param mixed $data Data
     *
     * @return string
     */
    public function sanitize($data)
    {
        return substr($data, 0, \XLite\Core\Database::getRepo($this->fieldClass)->getFieldInfo($this->fieldName, 'length'));
    }

    /**
     * Wrapper
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwSKUError()
    {
        throw $this->throwError('The field must be unique');
    }
}
