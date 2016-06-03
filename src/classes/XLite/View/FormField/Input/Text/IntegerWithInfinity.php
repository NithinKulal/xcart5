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
class IntegerWithInfinity extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/input/text/integer_with_infinity.js';

        return $list;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        $value = $this->getParam(static::PARAM_VALUE);

        return html_entity_decode('&#x221E;') === $value ? $value : parent::getValue();
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $list = array_diff(parent::assembleValidationRules(), array('custom[integer]'));
        $list[] = 'custom[integerWithInfinity]';

        return $list;
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
        $list = array_diff(parent::assembleClasses($classes), array('integer'));
        $list[] = 'with-infinity';
        $list[] = 'integer-with-infinity';

        return $list;
    }
}
