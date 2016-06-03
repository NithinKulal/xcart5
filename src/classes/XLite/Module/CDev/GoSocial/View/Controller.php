<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoSocial\View;

/**
 * Controller 
 */
abstract class Controller extends \XLite\View\Controller implements \XLite\Base\IDecorator
{
    /**
     * Get head prefixes
     *
     * @return array
     */
    public static function defineHTMLPrefixes()
    {
        $list = parent::defineHTMLPrefixes();

        $list['og'] = 'http://ogp.me/ns#';
        $list['fb'] = 'http://ogp.me/ns/fb#';

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_namespace) {
            $ns = \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_namespace;
            $list[$ns] = 'http://ogp.me/ns/' . $ns . '#';
        }

        return $list;
    }
}
