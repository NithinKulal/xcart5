<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Keys notice popup button
 */
class KeysNotice extends \XLite\View\Button\APopupButton
{
    /**
     * Widget parameters
     */
    const PARAM_FORCE_POPUP = 'forcePopup';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_FORCE_POPUP => new \XLite\Model\WidgetParam\TypeBool('Force popup display', true),
        ];
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/keys_notice.js';
        $list[] = 'button/js/enter_license_key.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'keys_notice/style.css';

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'License warning';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'    => 'keys_notice',
            'widget'    => '\XLite\View\ModulesManager\KeysNotice',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL(),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
        . ' keys-notice'
        . ($this->getParam(static::PARAM_FORCE_POPUP) ? ' force-notice' : '');
    }
}
