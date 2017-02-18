<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\View;

/**
 * Social sign-in widget
 */
class SocialLoginWidget extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    const PARAM_CAPTION      = 'caption';
    const PARAM_TEXT_BEFORE  = 'text_before';
    const PARAM_TEXT_AFTER   = 'text_after';
    const PARAM_PLACEMENT    = 'placement';
    const PARAM_BUTTON_STYLE = 'buttonStyle';

    /**
     * Get all configured authentication providers
     *
     * @return array Auth providers list
     */
    public function getAuthProviders()
    {
        return \XLite\Module\CDev\SocialLogin\Core\AuthManager::getAuthProviders();
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/SocialLogin/style.css';

        return $list;
    }

    /**
     * Return default template
     * See setWidgetParams()
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SocialLogin/social_login.twig';
    }

    /**
     * Check if widget is visible
     * (there should be at least one active auth provider)
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !\XLite::isAdminZone()
            && $this->getAuthProviders()
            && !\XLite\Core\Auth::getInstance()->isLogged();
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
            static::PARAM_CAPTION      => new \XLite\Model\WidgetParam\TypeString('Caption', null),
            static::PARAM_TEXT_BEFORE  => new \XLite\Model\WidgetParam\TypeString('TextBefore', null),
            static::PARAM_TEXT_AFTER   => new \XLite\Model\WidgetParam\TypeString('TextAfter', null),
            static::PARAM_PLACEMENT    => new \XLite\Model\WidgetParam\TypeString('Placement', null),
            static::PARAM_BUTTON_STYLE
                => new \XLite\Model\WidgetParam\TypeString('Button style', $this->defineButtonStyle()),
        );
    }

    /**
     * Get widget caption
     *
     * @return string
     */
    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    /**
     * Get widget's preceding text
     *
     * @return string
     */
    protected function getTextBefore()
    {
        return $this->getParam(static::PARAM_TEXT_BEFORE);
    }

    /**
     * Get widget's following text
     *
     * @return string
     */
    protected function getTextAfter()
    {
        return $this->getParam(static::PARAM_TEXT_AFTER);
    }

    /**
     * Get placement
     *
     * @return string
     */
    protected function getPlacement()
    {
        return $this->getParam(static::PARAM_PLACEMENT);
    }

    /**
     * Returns widget style class
     *
     * @return string
     */
    protected function getStyleClass()
    {
        $class = 'social-login-container';
        $class .= ' social-login-' . $this->getPlacement();

        return $class;
    }

    /**
     * Returns social button style
     *
     * @return string
     */
    protected function getButtonStyle()
    {
        return $this->getParam(static::PARAM_BUTTON_STYLE);
    }

    /**
     * Define default button style
     *
     * @return string
     */
    protected function defineButtonStyle()
    {
        return 'button';
    }
}
