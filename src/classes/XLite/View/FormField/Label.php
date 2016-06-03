<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField;

/**
 * Label
 */
class Label extends \XLite\View\FormField\Label\ALabel
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'label.twig';
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
        $classes[] = 'label-field';
        $classes[] = 'input';

        return $classes;
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return 'input input-label';
    }
}
