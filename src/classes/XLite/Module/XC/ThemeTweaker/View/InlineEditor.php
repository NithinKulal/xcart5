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
        $list = parent::getCommonFiles();

        if ($this->isInInlineEditingMode()) {
            $list[static::RESOURCE_JS][]    = 'js/content-tools/content-tools.js';
            $list[static::RESOURCE_CSS][]   = 'css/content-tools/content-tools.min.css';
            $list[static::RESOURCE_JS][]    = $this->getEditorLanguageResource();
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isInInlineEditingMode()) {
            $list[] = 'modules/XC/ThemeTweaker/inline_editable/editor_style.css';
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isInInlineEditingMode()) {
            $list[] = 'modules/XC/ThemeTweaker/inline_editable/controller.js';
            $list[] = 'modules/XC/ThemeTweaker/inline_editable/image_uploader.js';
        }

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
        return 'js/content-tools/translations/'
            . $this->getCurrentLanguage()->getCode()
            . '.js';
    }

    /**
     * Enables inline editing mode if current page is a product preview.
     *
     * @return boolean
     */
    protected function isInInlineEditingMode()
    {
        $controller = \XLite::getController();
        return $controller instanceof \XLite\Controller\Customer\Product
            && $controller->isPreview()
            && !\XLite\Core\Request::getInstance()->isInLayoutMode();
    }

    /**
     * Restrict template rendering
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
