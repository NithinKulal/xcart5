<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField;

/**
 * Coupon code
 */
class Code extends \XLite\View\FormField\Input\Text
{
    /**
     * Validation parameters
     */
    const MIN_SIZE = 4;
    const MAX_SIZE = 16;

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result && $this->getValue()) {
            $length = strlen($this->getValue());

            // Check size
            if (static::MIN_SIZE > $length) {
                // Too small
                $result = false;
                $this->errorMessage = static::t(
                    'The length of X field must be greater than Y',
                    array(
                        'name' => $this->getLabel(),
                        'min'  => static::MIN_SIZE,
                    )
                );
            } elseif (static::MAX_SIZE < $length) {
                // Too big
                $result = false;
                $this->errorMessage = static::t(
                    'The length of X field must be less than Y',
                    array(
                        'name' => $this->getLabel(),
                        'max'  => static::MAX_SIZE,
                    )
                );
            } else {
                // Check duplicate
                $modelId = \XLite\Core\Request::getInstance()->id;
                $model = $modelId
                    ? \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->find($modelId)
                    : null;
                $duplicates = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')
                    ->findDuplicates($this->getValue(), $model);

                if ($duplicates) {
                    $result = false;
                    $this->errorMessage = static::t(
                        'X code is already used for other coupon, please specify a different code',
                        array(
                            'code' => $this->getValue(),
                        )
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'minSize[' . static::MIN_SIZE . ']';
        $rules[] = 'maxSize[' . static::MAX_SIZE . ']';

        return $rules;
    }
}
