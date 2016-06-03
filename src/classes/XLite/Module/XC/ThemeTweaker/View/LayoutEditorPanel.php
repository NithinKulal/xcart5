<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Main panel of layout editing mode
 *
 * @ListChild (list="layout.main", zone="customer", weight="0")
 */
class LayoutEditorPanel extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/layout_editor/panel.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/ThemeTweaker/layout_editor/panel_style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/ThemeTweaker/layout_editor/panel_controller.js';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Request::getInstance()->isInLayoutMode()
            && !$this->isCheckoutLayout();
    }

    /**
     * Get finishOperateAs action url
     * 
     * @return string
     */
    protected function getFinishOperateAsUrl()
    {
        return $this->buildURL('login', 'logoff');
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    protected function getPreloadedLabels()
    {
        $list = array(
            'Enable',
            'Disable',
            'Save changes',
            'Exit editor',
            'Exiting...',
            'Layout editor is temporarily disabled',
            'Changes were successfully saved',
            'Unable to save changes',
            'You are now in layout editing mode',
            'You have unsaved changes. Are you really sure to exit the layout editor?'
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}

