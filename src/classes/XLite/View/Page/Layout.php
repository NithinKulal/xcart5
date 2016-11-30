<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Page;

/**
 * Layout page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Layout extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['layout']);
    }

    /**
     * Returns CSS style files
     *
     * @return string
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'layout_settings/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout_settings/body.twig';
    }
}