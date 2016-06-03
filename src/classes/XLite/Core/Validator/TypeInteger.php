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
class TypeInteger extends \XLite\Core\Validator\TypeFloat
{
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
        parent::validate($data);

        if (
            !is_int($data)
            && (!is_string($data) || !preg_match('/^\s*[+-]?\d+\s*$/Ssi', $data))
        ) {
            throw $this->throwError('Not a integer');
        }
    }

    /**
     * Sanitaize
     *
     * @param mixed $data Data
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        return intval($data);
    }

}
