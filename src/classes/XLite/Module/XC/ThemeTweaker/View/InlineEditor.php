<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

/**
 * Widget with resources for inline content editing
 *
 * @ListChild (list="layout.main", zone="customer", weight="0")
 */
class InlineEditor extends \XLite\View\AView
{
    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = array();

        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.pkgd.min.js';
        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.activate.js';
        $list[static::RESOURCE_CSS][]   = 'froala-editor/css/froala_editor.pkgd.min.css';
        $list[static::RESOURCE_JS][]    = $this->getEditorLanguageResource();

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = 'modules/XC/ThemeTweaker/inline_editable/panel_style.css';
        $list[] = 'modules/XC/ThemeTweaker/inline_editable/editor_style.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = array();

        $list[] = 'modules/XC/ThemeTweaker/inline_editable/inline_editable_controller.js';
        $list[] = 'modules/XC/ThemeTweaker/inline_editable/panel_controller.js';

        return $list;
    }

    /**
     * Return resource structure for content editor language file.
     * By default there are several ready-to-use language files from content editor project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getEditorLanguageResource()
    {
        return array(
            'file' => $this->getEditorLanguageFile(),
            'no_minify' => true,
        );
    }

    /**
     * Return content editor language file path.
     *
     * @return string
     */
    protected function getEditorLanguageFile()
    {
        return 'froala-editor/js/languages/'
            . $this->getCurrentLanguageCode()
            . '.js';
    }

    /**
     * Gets current language code and fixes it in case of en-GB and similar.
     *
     * @return string
     */
    protected function getCurrentLanguageCode()
    {
        $code = $this->getCurrentLanguage()->getCode();

        switch ($code) {
            case 'en':
                return 'en_gb';

            case 'pt':
                return 'pt_pt';

            case 'zh':
                return 'zh_cn';
            
            default:
                return $code;
        }
    }

    /**
     * Enables inline editing mode if current page is a product preview.
     *
     * @return boolean
     */
    public static function isInPreviewMode()
    {
        $controller = \XLite::getController();
        return $controller instanceof \XLite\Controller\Customer\Product
            && $controller->isPreview()
            && !\XLite\Core\Request::getInstance()->isInLayoutMode();
    }

    /**
     * Enables inline editing mode if current page is a product preview.
     *
     * @return boolean
     */
    public static function isInlineEditorAvailable()
    {
        return !static::isTinyMceEnabled();
    }

    /**
     * Determines if incompatible TinyMCE module is enabled
     *
     * @return boolean
     */
    public static function isTinyMceEnabled()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('CDev\TinyMCE');
    }

    /**
     * Checks if widget should be rendered
     * 
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && static::isInPreviewMode();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/inline_editable/panel.twig';
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
            'Exit product preview',
            'Exiting...',
            'Changes were successfully saved',
            'Unable to save changes',
            'You are now in preview mode',
            'You have unsaved changes. Are you really sure to exit the preview?',
            'Inline editor is unavailable due to TinyMCE',
            'Changes may be incompatible with TinyMCE. Are you sure to proceed?'
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}
