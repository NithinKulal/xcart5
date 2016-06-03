<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

/**
 * CommonResources widget
 */
abstract class CommonResources extends \XLite\View\CommonResources implements \XLite\Base\IDecorator
{
    /**
     * So called "static constructor".
     * NOTE: do not call the "parent::__constructStatic()" explicitly: it will be called automatically
     *
     * @return void
     */
    public static function __constructStatic()
    {
        static::$profilerInfo = array(
            'markTemplates' => \XLite\Module\XC\WebmasterKit\Core\Profiler::markTemplatesEnabled(),
            'countDeep'     => 0,
            'countLevel'    => 0,
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (static::$profilerInfo['markTemplates']) {
            $list[static::RESOURCE_JS][]  = 'modules/XC/WebmasterKit/template_debuger.js';
        }

        return $list;
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (static::$profilerInfo['markTemplates']) {
            $list[] = 'modules/XC/WebmasterKit/template_debuger.css';
        }

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/WebmasterKit/core.js';

        return $list;
    }
}

// Call static constructor
\XLite\Module\XC\WebmasterKit\View\CommonResources::__constructStatic();
