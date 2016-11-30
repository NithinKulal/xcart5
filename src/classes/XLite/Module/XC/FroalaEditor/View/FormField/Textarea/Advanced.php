<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FroalaEditor\View\FormField\Textarea;

/**
 * Froala textarea widget
 */
class Advanced extends \XLite\View\FormField\Textarea\Advanced implements \XLite\Base\IDecorator
{
    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/froala_editor.js';

        return $list;
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.pkgd.min.js';
        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.activate.js';
        $list[static::RESOURCE_CSS][]   = 'froala-editor/css/froala_editor.pkgd.min.css';
        $list[static::RESOURCE_JS][]    = $this->getEditorLanguageResource();

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
     * Return CSS files for this widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/css/style.css';

        return $list;
    }


    /**
     * getFieldTemplate
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '/form_field/textarea.twig';
    }


    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/FroalaEditor';
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'fr-instance';

        return $classes;
    }

    /**
     * Return structure of configuration for JS TinyMCE library
     *
     * @return array
     */
    protected function getFroalaConfiguration()
    {
        return array(
            'placeholderText' => '',
            'iframe' => true,
            'iframeStyleFiles' => $this->getIframeStyleFiles(),
            'toolbarSticky' => true,
            'charCounterCount' => false,
            'imageUploadURL' => \XLite\Core\Converter::buildURL(
                'files',
                'upload_from_file',
                array(
                    'mode' => 'json',
                    'type' => 'image',
                    'url_param_name' => 'link',
                    'register' => true
                )
            ),
            'imageManagerLoadURL' => \XLite\Core\Converter::buildURL(
                'files',
                'get_image_manager_list',
                array()
            ),
            'imageManagerDeleteURL' => \XLite\Core\Converter::buildURL(
                'files',
                'remove_from_image_manager',
                array()
            ),
            'imageUploadParam' => 'file',
            'requestHeaders' => array(
                'X-Requested-With' => 'XMLHttpRequest'
            ),
            'toolbarButtons' => array(
                'fontFamily', 'fontSize', '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color', 'inlineStyle', 'clearFormatting', '|', 'paragraphFormat', 'paragraphStyle', 'formatOL', 'formatUL', '-', 
                'align', 'indent', 'outdent', 'insertHR', 'quote', '|', 'insertImage', 'insertTable', 'insertLink', 'insertVideo', '|', 'undo', 'redo', 'html', 'fullscreen'
            )
        );
    }

    /**
     * Provides style files for iframe mode. Destined to replicate customer zone style.
     * 
     * @return array
     */
    protected function getIframeStyleFiles()
    {
        $styleFiles = array();

        $customer = $this->getCustomerLessStyles();

        if ($customer) {
            $styleFiles[] = $customer;
        }

        $styleFiles = array_merge($styleFiles, $this->getThemeStyles());
        $styleFiles = array_merge($styleFiles, $this->getFroalaEditorStyles());

        return $styleFiles;
    }

    /**
     * Returns compiled customer zone style file url.
     * 
     * @return string
     */
    protected function getCustomerLessStyles()
    {
        $lessParser = \XLite\Core\LessParser::getInstance();

        $customerLESS = array(
            array(
                'file' => \XLite\Core\Layout::getInstance()->getResourceFullPath('bootstrap/css/bootstrap.less', \XLite::COMMON_INTERFACE),
                'media' => 'screen',
                'weight' => 0,
                'filelist' => array(
                    'bootstrap/css/bootstrap.less',
                ),
                'interface' => \XLite::COMMON_INTERFACE,
                'original' => 'bootstrap/css/bootstrap.less',
                'url' => \XLite\Core\Layout::getInstance()->getResourceWebPath('bootstrap/css/bootstrap.less', \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, \XLite::COMMON_INTERFACE),
                'less' => true,
            ),
            array(
                'file'          => \XLite\Core\Layout::getInstance()->getResourceFullPath('css/style.less', \XLite::CUSTOMER_INTERFACE),
                'media'         => 'screen',
                'merge'         => 'bootstrap/css/bootstrap.less',
                'filelist'      => array(
                    'css/style.less',
                ),
                'interface'     => null,
                'original'      => 'css/style.less',
                'url'           => \XLite\Core\Layout::getInstance()->getResourceWebPath('css/style.less', \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, \XLite::CUSTOMER_INTERFACE),
                'less'          => true
            ),
        );

        // Customer LESS files parsing
        $lessParser->setInterface('default');

        $lessParser->setHttp('http');
        $style = $lessParser->makeCSS($customerLESS);

        if ($style && isset($style['url'])) {
            return $style['url'];
        }

        return null;
    }

    /**
     * Returns current theme style files
     * 
     * @return array
     */
    protected function getThemeStyles()
    {
        $themeFiles = $this->getThemeFiles(false);
        $themeFiles = $themeFiles[static::RESOURCE_CSS];
        $themeFilesCSS = array();

        foreach ($themeFiles as $key => $file) {
            if (!is_array($file)) {
                $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    $file,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::CUSTOMER_INTERFACE
                );

                if ($path) {
                    $themeFilesCSS[] = $this->getShopURL($path, null, array('t' => LC_START_TIME));
                }
            }
        }

        return $themeFilesCSS;
    }

    /**
     * Returns specific froala editor styles to be used inside iframe
     * 
     * @return array
     */
    protected function getFroalaEditorStyles()
    {
        return array(
            $this->getShopURL(
                \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    'froala-editor/css/froala_style.fixed.css',
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::COMMON_INTERFACE
                ),
                null, array('t' => LC_START_TIME)
            ),
            $this->getShopURL(
                \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    'froala-editor/css/froala_style.iframe.css',
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::COMMON_INTERFACE
                ),
                null, array('t' => LC_START_TIME)
            ),
        );
    }

    /**
     * Get processed value 
     * 
     * @return string
     */
    protected function getProcessedValue()
    {
        return str_replace(
            \XLite\Model\Base\Catalog::WEB_LC_ROOT,
            htmlentities(\XLite::getInstance()->getShopURL(null)),
            $this->getValue()
        );
    }
}
