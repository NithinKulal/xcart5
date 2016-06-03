<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Common resources loader
 *
 * @ListChild (list="admin.center", zone="admin")
 * @ListChild (list="layout.main", zone="customer")
 */
class CommonResources extends \XLite\View\AView
{
    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        return array(
            array('getCommonFiles', 50, \XLite::COMMON_INTERFACE),
            array('getResources',   60, null),
            array('getThemeFiles',  70, null),
            array('getPrintFiles',  400, null),
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        return array(
            static::RESOURCE_JS => array(
                array(
                    'file'      => 'js/jquery.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/jquery-migrate.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/jquery-ui.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/jquery.ui.touch-punch.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/jquery.cookie.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/underscore-min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/underscore.string.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'bootstrap/js/bootstrap.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/hash.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/object_hash.js',
                    'no_minify' => true,
                ),
                $this->getValidationEngineLanguageResource(),
                array(
                    'file'      => 'js/validationEngine.min/jquery.validationEngine.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/validationEngine.min/custom.validationEngine.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/jquery.mousewheel.min.js',
                    'no_minify' => true,
                ),
                'js/regex-mask-plugin.js',
                'js/common.js',
                'js/core.element.js',
                'js/core.js',
                'js/core.extend.js',
                'js/core.controller.js',
                'js/core.loadable.js',
                'js/core.utils.js',
                'js/lazyload.js',
                'js/core.popup.js',
                'js/core.popup_button.js',
                'js/core.form.js',
                array(
                    'file'      => 'js/php.min.js',
                    'no_minify' => true,
                ),
                array(
                    'file'      => 'js/fallback.min.js',
                    'no_minify' => true,
                ),
            ),
            static::RESOURCE_CSS => array(
                'css/normalize.css',
                'ui/jquery-ui.css',
                'css/jquery.mousewheel.css',
                'css/validationEngine.jquery.css',
                'css/font-awesome/font-awesome.min.css',
                array(
                    'file'      => 'bootstrap/css/initialize.less',
                    'media'     => 'screen',
                    'weight'    => 0,
                ),
                array(
                    'file'      => 'bootstrap/css/bootstrap.less',
                    'media'     => 'screen',
                    'weight'    => 0,
                ),
                array(
                    'url' => '//fonts.googleapis.com/css?family='
                        . urlencode('Open Sans:300italic,400italic,600italic,700italic,400,300,600,700')
                        . '&subset='
                        . urlencode('latin,cyrillic,latin-ext'),
                    'media' => 'not print',
                ),
            ),
        );
    }

    /**
     * Return theme common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        return (null === $adminZone ? \XLite::isAdminZone() : $adminZone)
            ? array(
                static::RESOURCE_CSS => array(
                    'css/style.css',
                    'css/ajax.css',
                ),
            ) : array(
                static::RESOURCE_CSS => array(
                    'css/theme.css',
                    'css/style.css',
                    'css/ajax.css',
                ),
            );
    }

    /**
     * Return print common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getPrintFiles($adminZone = null)
    {
        return array(
            static::RESOURCE_CSS => array(
                array(
                    'file' => 'css/print.css',
                    'media' => 'print',
                ),
            ),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
