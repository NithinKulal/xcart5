<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\TinyMCE\View\FormField\Textarea;

/**
 * TinyMCE textarea widget
 */
class Advanced extends \XLite\View\FormField\Textarea\Advanced implements \XLite\Base\IDecorator
{
    const PARAM_CONVERT_URLS = 'convertUrls';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CONVERT_URLS     => new \XLite\Model\WidgetParam\TypeBool('Convert urls', true),
        );
    }

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/init.tinymce.js';
        $list[] = $this->getDir() . '/js/tinymce/tinymce.min.js';
        $list[] = $this->getDir() . '/js/script.js';

        return $list;
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
        return 'modules/CDev/TinyMCE';
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

        $classes[] = 'tinymce';

        return $classes;
    }

    /**
     * Return structure of configuration for JS TinyMCE library
     *
     * @return array
     */
    protected function getTinyMCEConfiguration()
    {
        $layout = \XLite\Core\Layout::getInstance();
        $themeFiles = $this->getThemeFiles(false);
        $themeFiles = $themeFiles[static::RESOURCE_CSS];
        $themeFilesCSS = array();
        foreach ($themeFiles as $key => $file) {
            if (!is_array($file)) {
                $path = $layout->getResourceWebPath(
                    $file,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::CUSTOMER_INTERFACE
                );

                if ($path) {
                    $themeFilesCSS[] = $this->getShopURL($path, null, array('t' => LC_START_TIME));
                }
            }
        }

        $contentCSS = implode(',', $themeFilesCSS);

        // Base is the web path to the tinymce library directory
        return array(
            'contentCSS'    => $contentCSS,
            'shopURL'       => \XLite\Core\URLManager::getShopURL(
                null,
                \XLite\Core\Request::getInstance()->isHTTPS(),
                array(),
                null,
                false // Remove $xid parameter
            ),
            'convertUrls'   => $this->getParam(static::PARAM_CONVERT_URLS),
            'shopURLRoot'   => \XLite\Model\Base\Catalog::WEB_LC_ROOT,
            'bodyClass'     => $this->getParam(static::PARAM_STYLE),
            'base'          => dirname(\XLite\Singletons::$handler->layout->getResourceWebPath(
                $this->getDir() . '/js/tinymce/tiny_mce.js',
                \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
            )) . '/',
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
