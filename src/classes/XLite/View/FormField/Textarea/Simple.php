<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Textarea;

/**
 * Textarea
 */
class Simple extends \XLite\View\FormField\Textarea\ATextarea
{
    /**
     * Widget param names
     */
    const PARAM_MIN_HEIGHT = 'maxWidth';
    const PARAM_MAX_HEIGHT = 'maxHeight';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_MIN_HEIGHT  => new \XLite\Model\WidgetParam\TypeInt('Min. height', 0),
            static::PARAM_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt('Max. height', 0),
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if ($this->getParam(static::PARAM_MAX_HEIGHT)) {
            $list[static::RESOURCE_JS][] = 'js/jquery.textarea-expander.js';
        }

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'textarea.twig';
    }

    /**
     * setCommonAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $attrs = parent::setCommonAttributes($attrs);

        if ($this->getParam(static::PARAM_MAX_HEIGHT)) {

            if ($this->getParam(static::PARAM_MIN_HEIGHT)) {
                $attrs['data-min-size-height'] = $this->getParam(static::PARAM_MIN_HEIGHT);
            }

            if ($this->getParam(static::PARAM_MAX_HEIGHT)) {
                $attrs['data-max-size-height'] = $this->getParam(static::PARAM_MAX_HEIGHT);
            }

            if (empty($attrs['class'])) {
                $attrs['class'] = '';
            }

            $attrs['class'] = trim($attrs['class'] . ' resizeble-txt');
        }

        unset($attrs['value']);

        return $attrs;
    }
}
