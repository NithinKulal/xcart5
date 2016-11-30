<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Abstract button
 */
abstract class AButton extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_NAME     = 'buttonName';
    const PARAM_VALUE    = 'value';
    const PARAM_LABEL    = 'label';
    const PARAM_STYLE    = 'style';
    const PARAM_DISABLED = 'disabled';
    const PARAM_ID       = 'id';
    const PARAM_ATTRIBUTES = 'attributes';
    const PARAM_BTN_SIZE   = 'button-size';
    const PARAM_BTN_TYPE   = 'button-type';
    const PARAM_ICON_STYLE = 'icon-style';
    const PARAM_JS_CONFIRM_TEXT = 'jsConfirmText';

    const BTN_SIZE_DEFAULT = 'btn-default';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/button.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/button.js';

        return $list;
    }

    /**
     * Define the divider button (in cases of buttons list)
     *
     * @return boolean
     */
    public function isDivider()
    {
        return false;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/regular.twig';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return '--- Button title is not defined ---';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return '';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultButtonClass()
    {
        return 'btn';
    }

    /**
     * getDefaultDisableState
     *
     * @return boolean
     */
    protected function getDefaultDisableState()
    {
        return false;
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getDefaultAttributes()
    {
        return array();
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = $this->getParam(static::PARAM_ATTRIBUTES);

        return array_merge($list, $this->getButtonAttributes());
    }

    /**
     * Defines the button specific attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {
        $list = array(
            'type' => 'button',
        );

        $class = $this->getClass();
        if ($class) {
            $list['class'] = $class;
        }

        if ($this->getId()) {
            $list['id'] = $this->getId();
        }

        if ($this->isDisabled()) {
            $list['disabled'] = 'disabled';
        }

        return $list;
    }

    /**
     * Return button text
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return $this->getParam(static::PARAM_LABEL);
    }

    /**
     * Return text for js confirm() function
     *
     * @return string
     */
    protected function getJsConfirmText()
    {
        return $this->getParam(static::PARAM_JS_CONFIRM_TEXT);
    }

    /**
     * Get commented data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return array();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_NAME     => new \XLite\Model\WidgetParam\TypeString('Name', '', true),
            static::PARAM_VALUE    => new \XLite\Model\WidgetParam\TypeString('Value', '', true),
            static::PARAM_LABEL    => new \XLite\Model\WidgetParam\TypeString('Label', $this->getDefaultLabel(), true),
            static::PARAM_STYLE    => new \XLite\Model\WidgetParam\TypeString('Button style', $this->getDefaultStyle()),
            static::PARAM_BTN_SIZE => new \XLite\Model\WidgetParam\TypeString('Button size', $this->getDefaultButtonSize()),
            static::PARAM_BTN_TYPE => new \XLite\Model\WidgetParam\TypeString('Button type', $this->getDefaultButtonType()),
            static::PARAM_DISABLED => new \XLite\Model\WidgetParam\TypeBool('Disabled', $this->getDefaultDisableState()),
            static::PARAM_ID       => new \XLite\Model\WidgetParam\TypeString('Button ID', ''),
            static::PARAM_ATTRIBUTES => new \XLite\Model\WidgetParam\TypeCollection('Attributes', $this->getDefaultAttributes()),
            static::PARAM_ICON_STYLE => new \XLite\Model\WidgetParam\TypeString('Button ID', ''),
            static::PARAM_JS_CONFIRM_TEXT => new \XLite\Model\WidgetParam\TypeString('JS confirm text', ''),
        );
    }

    /**
     * Define the size of the button.
     *
     * @return string
     */
    protected function getDefaultButtonSize()
    {
        return '';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'regular-button';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return $this->getDefaultButtonClass() . ' '
            . $this->getParam(static::PARAM_BTN_SIZE) . ' '
            . $this->getParam(static::PARAM_BTN_TYPE) . ' '
            . $this->getParam(static::PARAM_STYLE)
            . ($this->isDisabled() ? ' disabled' : '');
    }

    /**
     * getId
     *
     * @return string
     */
    protected function getId()
    {
        return $this->getParam(static::PARAM_ID);
    }

    /**
     * Return button name
     *
     * @return string
     */
    protected function getName()
    {
        return $this->getParam(static::PARAM_NAME);
    }

    /**
     * Return button value
     *
     * @return string
     */
    protected function getValue()
    {
        return $this->getParam(static::PARAM_VALUE);
    }

    /**
     * hasName
     *
     * @return void
     */
    protected function hasName()
    {
        return '' !== $this->getParam(static::PARAM_NAME);
    }

    /**
     * hasValue
     *
     * @return void
     */
    protected function hasValue()
    {
        return '' !== $this->getParam(static::PARAM_VALUE);
    }

    /**
     * hasClass
     *
     * @return string
     */
    protected function hasClass()
    {
        return '' !== $this->getParam(static::PARAM_STYLE);
    }

    /**
     * isDisabled
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return $this->getParam(static::PARAM_DISABLED);
    }
}
