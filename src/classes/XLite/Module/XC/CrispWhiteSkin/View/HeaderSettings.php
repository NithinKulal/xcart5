<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View;

/**
 * Product comparison widget
 *
 * @ListChild (list="layout.header.right", weight="30")
 */
class HeaderSettings extends \XLite\View\AView
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/header_settings.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = array(
            'file'  => 'css/header_settings.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'layout/header/header.right.settings.twig';
    }
}
