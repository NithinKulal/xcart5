<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Page not found block
 *
 * @ListChild (list="center")
 * @ListChild (list="admin.center", zone="admin")
 */
class PageNotFound extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = \XLite::TARGET_404;

        return $result;
    }

    /**
     * Add NOINDEX in meta tags
     *
     * @return array
     */
    public function getMetaTags()
    {
        $list = parent::getMetaTags();
        $list[] = '<meta name="robots" content="noindex,nofollow"/>';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return '404.twig';
    }

}

