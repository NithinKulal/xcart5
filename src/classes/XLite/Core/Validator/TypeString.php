<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * String
 */
class TypeString extends \XLite\Core\Validator\Scalar
{
    /**
     * Non-empty validation flag
     *
     * @var mixed
     */
    protected $nonEmpty = false;

    /**
     * Constructor
     *
     * @param boolean $nonEmpty Non-empty flag OPTIONAL
     */
    public function __construct($nonEmpty = false)
    {
        parent::__construct();

        $this->markAsNonEmpty($nonEmpty);
    }

    /**
     * Mark validator as required non-empty
     *
     * @param boolean $nonEmpty Flag OPTIONAL
     *
     * @return void
     */
    public function markAsNonEmpty($nonEmpty = true)
    {
        $this->nonEmpty = $nonEmpty;
    }

    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    public function validate($data)
    {
        if (!is_string($data)) {
            throw $this->throwError($this->getNotAStringError());
        }

        if ($this->nonEmpty && 0 == strlen($data)) {
            throw $this->throwError($this->getEmptyValueError());
        }
    }

    /**
     * Return not a string error message
     *
     * @return string
     */
    protected function getNotAStringError()
    {
        return 'Not a string';
    }

    /**
     * Return empty value error message
     *
     * @return string
     */
    protected function getEmptyValueError()
    {
        return 'Value is empty';
    }
}
