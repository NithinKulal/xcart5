<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'copy this' button widget
 */
class CopyLink extends \XLite\View\Button\AButton
{
    /**
     * Several inner constants
     */
    const BUTTON_JS  = 'button/js/copy_link.js';
    const BUTTON_CSS = 'button/css/copy_link.css';

    /**
     * Widget parameters to use
     */
    const PARAM_COPY_LINK  = 'copy';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = self::BUTTON_JS;

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = array(
            'file'      => 'js/clipboard.min.js',
            'no_minify' => true,
        );

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = self::BUTTON_CSS;

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/copy_link.twig';
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
            self::PARAM_COPY_LINK  => new \XLite\Model\WidgetParam\TypeString('Copy Link', '', true),
        );
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'button copy-link';
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getIconStyle()
    {
        return 'fa fa-clipboard';
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getCopyURL()
    {
        return $this->getParam(self::PARAM_COPY_LINK)
            ?: '';
    }

    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getCommentedData()
    {
        return array(
            'link' => $this->getCopyURL(),
        );
    }

    /**
     * Get default attributes
     *
     * @return array
     */
    protected function getButtonAttributes()
    {

        return parent::getButtonAttributes() + array(
            'data-clipboard-text' => $this->getCopyURL(),
        );
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Copy';
    }
}
