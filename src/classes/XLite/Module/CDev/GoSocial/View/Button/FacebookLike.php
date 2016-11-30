<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View\Button;

/**
 * Facebook Like button
 *
 * @ListChild (list="buttons.share", weight="50")
 */
class FacebookLike extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_WIDTH  = 'width';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/GoSocial/button/js/facebook_like.js';

        return $list;
    }

    /**
     * Get width
     *
     * @return integer
     */
    protected function getWidth()
    {
        return $this->getParam(self::PARAM_WIDTH);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoSocial/button/facebook_like.twig';
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
            self::PARAM_WIDTH => new \XLite\Model\WidgetParam\TypeInt('Width', $this->getDefaultWidth()),
        );
    }

    /**
     * Get defaul width
     *
     * @return integer
     */
    protected function getDefaultWidth()
    {
        switch ($this->getLayoutStyle()) {
            case 'button_count':
                $width = 90;
                break;

            case 'box_count':
                $width = 55;
                break;

            case 'standard':
                $width = 450;
                break;

            default:
                $width = 0;
        }

        return $width;
    }


    /**
     * Get like button layout
     * 
     * @return mixed
     */
    protected function getLayoutStyle()
    {
        return \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_layout;
    }

    /**
     * Get button attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {
        $attributes = array(
            'send'        => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_send_button ? 'true' : 'false',
            'layout'      => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_layout,
            'colorscheme' => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_colorscheme,
            'show-faces'  => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_show_faces ? 'true' : 'false',
            'action'      => \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_verb,
        );

        if (0 < $this->getWidth()) {
            $attributes['width'] = $this->getWidth();
        }

        return $attributes;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
        && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_use
        && \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_id;
    }
}
