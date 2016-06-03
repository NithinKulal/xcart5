<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator;

/**
 * Integer
 */
class TypeFloat extends \XLite\Core\Validator\Scalar
{
    /**
     * Range minimum
     *
     * @var float
     */
    protected $min;

    /**
     * Range maximum
     *
     * @var float
     */
    protected $max;

    /**
     * Set range
     *
     * @param float $min Minimum
     * @param float $max Maximum OPTIONAL
     *
     * @return void
     */
    public function setRange($min, $max = null)
    {
        if (isset($min) && is_numeric($min)) {
            $this->min = $min;
        }

        if (isset($max) && is_numeric($max)) {
            $this->max = $max;
        }
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
        if (!is_numeric($data)) {
            throw $this->throwError('Not numeric');
        }

        $data = $this->sanitize($data);

        if (isset($this->min) && $data < $this->min) {
            throw $this->throwError('Minimum limit is broken', array('min' => $this->min));
        }

        if (isset($this->max) && $data > $this->max) {
            throw $this->throwError('Maximum limit is broken', array('max' => $this->max));
        }
    }

    /**
     * Sanitaize
     *
     * @param mixed $data Daa
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        return doubleval($data);
    }

}
