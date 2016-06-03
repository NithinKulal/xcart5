<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Integer
 */
class Integer extends \XLite\View\FormField\Input\Text\Base\Numeric
{
    const SANITIZE_PATTERN = '/[^\d-]/sS';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/input/text/integer.js';

        return $list;
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return intval(preg_replace(static::SANITIZE_PATTERN, '', parent::prepareRequestData($value)));
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return intval(preg_replace(static::SANITIZE_PATTERN, '', parent::sanitize()));
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'custom[integer]';

        return $rules;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'integer';

        return $classes;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 11;
    }
}
