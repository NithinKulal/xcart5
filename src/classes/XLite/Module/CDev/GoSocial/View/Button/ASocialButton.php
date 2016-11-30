<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;


/**
 * Class ASocialButton
 * @package XLite\Module\CDev\GoSocial\View\Button
 */
abstract class ASocialButton extends \XLite\View\AView
{
    const BUTTON_CLASS_FACEBOOK = 'facebook';
    const BUTTON_CLASS_TWITTER = 'twitter';
    const BUTTON_CLASS_GOOGLEPLUS = 'gplus';
    const BUTTON_CLASS_PINTEREST = 'pinterest';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/GoSocial/likely.js';

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
        $list[] = 'modules/CDev/GoSocial/likely.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/social_button.twig';
    }
    
    /**
     * Get button type
     * 
     * @return string
     */
    abstract function getButtonType();
    
    /**
     * Get button label
     * 
     * @return string
     */
    abstract function getButtonLabel();

    /**
     * The link caption that should be posted to the social networks. By default it’s the page’s title.
     *
     * @return string
     */
    protected function getDataTitle()
    {
        return null;
    }

    /**
     * Return a link that should be posted to the social networks. By default it’s the address of the page where Likely is used.
     *
     * @return string
     */
    protected function getDataURL()
    {
        return \XLite::getInstance()->getShopURL($this->getURL());
    }

    /**
     * Define array of params
     *
     * @return array
     */
    protected function defineButtonParams()
    {
        return [];
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getButtonParamsCode()
    {
        $result = '';

        foreach ($this->defineButtonParams() as $name => $value) {
            $result .= ' ' . $name . '="' . func_htmlspecialchars($value) . '"';
        }

        return $result;
    }
}