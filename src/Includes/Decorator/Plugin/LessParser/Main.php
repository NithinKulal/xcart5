<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\LessParser;

if (!defined('LC_CACHE_BUILDING_FINISH')) {
    define('LC_CACHE_BUILDING_FINISH', true);
}

/**
 * Customer LESS parser
 */
abstract class Main extends \Includes\Decorator\Plugin\APlugin
{
    /**
     * Define the LESS files structure
     *
     * @return array
     */
    protected static function getLESS($interface)
    {
        $list = \XLite\Core\Layout::getInstance()->getLESSResources($interface);

        $commonBootstrap = array(
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
        );

        $result = array($commonBootstrap);

        foreach ($list as $less) {
            $result[] = array(
                'file'          => \XLite\Core\Layout::getInstance()->getResourceFullPath($less, $interface),
                'media'         => 'screen',
                'merge'         => 'bootstrap/css/bootstrap.less',
                'filelist'      => array(
                    $less,
                ),
                'interface'     => null,
                'original'      => $less,
                'url'           => \XLite\Core\Layout::getInstance()->getResourceWebPath($less, \XLite\Core\Layout::WEB_PATH_OUTPUT_SHORT, $interface),
                'less'          => true,
            );
        }

        return $result;
    }
}
