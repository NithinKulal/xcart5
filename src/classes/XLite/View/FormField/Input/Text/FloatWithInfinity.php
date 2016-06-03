<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Float
 */
class FloatWithInfinity extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/input/text/float_with_infinity.js';

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
        $list = array_diff(parent::assembleValidationRules(), array('custom[number]'));
        $list[] = 'custom[numberWithInfinity]';

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
        $list = array_diff(parent::assembleClasses($classes), array('float'));
        $list[] = 'with-infinity';
        $list[] = 'float-with-infinity';

        return $list;
    }
}
