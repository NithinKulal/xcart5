<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Login;

use XLite\Model\WidgetParam\TypeString;
use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * Social sign-in widget
 */
class Widget extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_CAPTION      = 'caption';
    const PARAM_TEXT_BEFORE  = 'text_before';
    const PARAM_TEXT_AFTER   = 'text_after';
    const PARAM_BUTTON_STYLE = 'buttonStyle';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/login/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/login/controller.js';

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
        return 'modules/Amazon/PayWithAmazon/login/widget.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $method    = Main::getMethod();
        $processor = Main::getProcessor();

        return parent::isVisible()
        && $processor->isConfigured($method)
        && $method->isEnabled()
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

        $this->widgetParams += [
            static::PARAM_CAPTION      => new TypeString('Caption', null),
            static::PARAM_TEXT_BEFORE  => new TypeString('TextBefore', null),
            static::PARAM_TEXT_AFTER   => new TypeString('TextAfter', null),
            static::PARAM_BUTTON_STYLE => new TypeString('Button style', $this->defineButtonStyle()),
        ];
    }

    /**
     * Get auth url
     *
     * @return string
     */
    protected function getAuthURL()
    {
        return $this->buildURL('amazon_login');
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

    /**
     * @return string
     */
    protected function getHash()
    {
        return md5(microtime());
    }
}
