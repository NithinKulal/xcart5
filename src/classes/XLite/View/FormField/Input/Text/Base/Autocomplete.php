<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text\Base;

/**
 * Autocomplete 
 */
abstract class Autocomplete extends \XLite\View\FormField\Input\Text
{
    /**
     * Get dictionary name
     * 
     * @return string
     */
    abstract protected function getDictionary();

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/input/text/autocomplete.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/input/text/autocomplete.css';

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
        $classes = parent::assembleClasses($classes);

        $classes[] = 'auto-complete';

        return $classes;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $attrs = parent::setCommonAttributes($attrs);

        $attrs['data-source-url'] = $this->getURL();

        return $attrs;
    }

    /**
     * Get URL 
     * 
     * @return string
     */
    protected function getURL()
    {
        return \XLite\Core\Converter::buildURL(
            'autocomplete',
            '',
            array('dictionary' => $this->getDictionary(), 'term' => '%term%')
        );
    }

    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        return trim(parent::getValueContainerClass() . ' autocomplete-field');
    }
}

