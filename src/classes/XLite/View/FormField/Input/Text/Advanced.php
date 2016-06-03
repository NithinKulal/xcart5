<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * \XLite\View\FormField\Input\Text\Advanced
 */
class Advanced extends \XLite\View\FormField\Input\Text
{

    /**
     * Widget catalog
     */
    const WIDGET_DIR = '/advanced_text_input';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . static::WIDGET_DIR . '/script.js';

        return $list;
    }

    /**
     * getCSSFiles
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . static::WIDGET_DIR . '/style.css';

        return $list;
    }

    /**
     * getLabel
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getValue() ?: parent::getLabel();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return static::WIDGET_DIR . '/input.twig';
    }

    /**
     * getParentFieldTemplate
     *
     * @return string
     */
    protected function getParentFieldTemplate()
    {
        return parent::getFieldTemplate();
    }
}
