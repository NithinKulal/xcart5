<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * SKU
 */
class SKU extends \XLite\View\FormField\Input\Text
{
    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result && $this->getValue()) {

            $validator = new \XLite\Core\Validator\SKU($this->getProductId());

            try {
                $validator->validate($this->getValue());

            } catch (\XLite\Core\Validator\Exception $exception) {
                $message = static::t($exception->getMessage(), $exception->getLabelArguments());
                $result = false;
                $this->errorMessage = \XLite\Core\Translation::lbl(
                    ($exception->getPublicName() ? static::t($exception->getPublicName()) . ': ' : '') . $message,
                    array(
                        'name' => $this->getLabel(),
                    )
                );
            }
        }

        return $result;
    }
}
