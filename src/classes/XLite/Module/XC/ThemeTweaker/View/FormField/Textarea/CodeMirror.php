<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormField\Textarea;

/**
 * Textarea
 */
class CodeMirror extends \XLite\View\FormField\Textarea\Simple
{
    /**
     * Widget param names
     */
    const PARAM_CODE_MODE = 'codeMode';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/codemirror/lib/codemirror.css';
        $list[] = 'modules/XC/ThemeTweaker/form_field/codemirror.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = array(
            'file'      => 'modules/XC/ThemeTweaker/codemirror/lib/min/codemirror.js',
            'no_minify' => true,
        );
        $list[] = 'modules/XC/ThemeTweaker/form_field/codemirror.js';

        $mode = $this->getParam(static::PARAM_CODE_MODE);

        if ($mode) {
            $list[] = sprintf('modules/XC/ThemeTweaker/codemirror/mode/%s/%s.js', $mode, $mode);
        }

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CODE_MODE  => new \XLite\Model\WidgetParam\TypeString('Mode', ''),
        );
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
        $classes[] = 'codemirror';

        return $classes;
    }

    /**
     * getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = parent::getAttributes();
        $attributes['data-codemirror-mode'] = $this->getParam(static::PARAM_CODE_MODE);

        return $attributes;
    }
}
