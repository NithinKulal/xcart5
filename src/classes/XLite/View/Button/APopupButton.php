<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Button to use with popup
 */
abstract class APopupButton extends \XLite\View\Button\AButton
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    abstract protected function prepareURLParams();

    /**
     * Return array of URL params for JS
     *
     * @return array
     */
    public function getURLParams()
    {
        $params = array(
            'url_params' => $this->prepareURLParams(),
        );

        if ($this->getJSConfirmText()) {
            $params['jsConfirm'] = $this->getJSConfirmText();
        }

        return $params;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/popup.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/core.popup.js';
        $list[static::RESOURCE_JS][] = 'js/core.popup_button.js';

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getButtonContent()
    {
        return $this->getParam(static::PARAM_LABEL) ?: $this->getDefaultLabel();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_button.twig';
    }

    /**
     * Defines CSS class for widget to use in templates
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' popup-button';
    }
}
