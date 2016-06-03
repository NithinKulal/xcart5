<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View;

/**
 * Abstract sign-in button
 */
abstract class AButton extends \XLite\View\AView
{
    /**
     * Social network related data
     */
    const DISPLAY_NAME = '';
    const FONT_AWESOME_CLASS = '';

    /**
     * Widget style
     */
    const PARAM_WIDGET_STYLE = 'widgetStyle';

    /**
     * Returns an instance of auth provider
     *
     * @return \XLite\Module\CDev\SocialLogin\Core\AAuthProvider
     */
    abstract protected function getAuthProvider();

    /**
     * Get widget display name
     *
     * @return string
     */
    public function getName()
    {
        return static::DISPLAY_NAME;
    }

    /**
     * Get provider font awesome class
     *
     * @return string
     */
    public function getFontAwesomeClass()
    {
        return static::FONT_AWESOME_CLASS;
    }

    /**
     * Get authentication request url
     *
     * @return string
     */
    public function getAuthRequestUrl()
    {
        $state = get_class(\XLite::getController());

        return $this->getAuthProvider()->getAuthRequestUrl($state);
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/SocialLogin/button/style.css';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SocialLogin/button/body.twig';
    }

    /**
     * Check if widget is visible
     * (auth provider must be fully configured)
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getAuthProvider()->isConfigured();
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
            static::PARAM_WIDGET_STYLE
                => new \XLite\Model\WidgetParam\TypeString('Widget style', $this->defineWidgetStyle()),
        );
    }

    /**
     * Define default widget style
     *
     * @return string
     */
    protected function defineWidgetStyle()
    {
        return 'button';
    }

    /**
     * Returns button style class
     *
     * @return string
     */
    protected function getStyleClass()
    {
        $class = 'social-net-element';
        $class .= ' social-net-' . $this->getParam(static::PARAM_WIDGET_STYLE);
        $class .= ' social-net-' . $this->getName();

        return $class;
    }
}
