<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;


/**
 * \XLite\View\FormField\Input\AInput
 */
abstract class AInput extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    const PARAM_PLACEHOLDER   = 'placeholder';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PLACEHOLDER => new \XLite\Model\WidgetParam\TypeString('Placeholder', $this->getDefaultPlaceholder()),
        );
    }

    /**
     * Get default placeholder
     *
     * @return string
     */
    protected function getDefaultPlaceholder()
    {
        return '';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input.twig';
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if ($this->getParam(static::PARAM_PLACEHOLDER)) {
            $list['placeholder'] = $this->getParam(static::PARAM_PLACEHOLDER);
        }

        return array_merge($list, array(
            'type'  => $this->getFieldType(),
            'value' => $this->getValue(),
        ));
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array();
    }
}
