<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MaxLengthValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $stringValue = (string) $value;
        $length = mb_strlen($stringValue);

        if (null !== $constraint->length && $length > $constraint->length) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->addViolation();
        }
    }
}
